<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use App\Models\Order;
use App\Models\VehicleType;
use App\Models\DriverDocument;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_drivers' => Driver::count(),
            'active_drivers' => Driver::where('is_online', true)->count(),
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'pending_orders' => Order::whereIn('status', ['pending', 'accepted', 'in_progress'])->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('platform_commission'),
            'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
            'pending_driver_approvals' => Driver::where('is_verified', 0)->count(),
            'pending_documents' => DriverDocument::where('status', 'pending')->count(),
        ];

        $recent_orders = Order::with(['customer', 'driver'])
            ->latest()
            ->take(10)
            ->get();

        $pending_drivers = Driver::with('user')
            ->where('is_verified', 0)
            ->latest()
            ->take(5)
            ->get();

        $monthly_revenue = Order::where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(platform_commission) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->toArray();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'pending_drivers', 'monthly_revenue'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Append query parameters to pagination links
        $users->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function userDetail(User $user)
    {
        // Load relationships based on user role
        if ($user->isDriver()) {
            $user->load(['driver', 'driver.orders']);
        } else {
            $user->load(['orders']);
        }
        
        return view('admin.users.show', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'role' => 'required|in:customer,driver,admin',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'role']));

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully');
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "User {$status} successfully");
    }

    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    public function drivers(Request $request)
    {
        $query = Driver::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function($q) use ($search) {
                // Search in user relationship
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%")
                             ->orWhere('phone', 'LIKE', "%{$search}%");
                })
                // Search in driver fields
                ->orWhere('license_number', 'LIKE', "%{$search}%")
                ->orWhere('vehicle_plate', 'LIKE', "%{$search}%")
                ->orWhere('vehicle_brand', 'LIKE', "%{$search}%")
                ->orWhere('vehicle_model', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Verification filter
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->get('is_verified'));
        }

        // Vehicle type filter
        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->get('vehicle_type'));
        }

        $drivers = $query->orderBy('created_at', 'desc')->paginate(20);
        $vehicle_types = ['motorcycle', 'car'];
        
        // Append query parameters to pagination links
        $drivers->appends($request->query());

        return view('admin.drivers.index', compact('drivers', 'vehicle_types'));
    }

    public function driverDetail(Driver $driver)
    {
        $driver->load(['user', 'vehicleType', 'orders', 'documents']);
        return view('admin.drivers.show', compact('driver'));
    }

    public function approveDriver(Driver $driver)
    {
        $driver->update(['is_verified' => 1]);
        
        // Pass the driver object instead of user_id
        $this->notificationService->sendDriverNotification(
            $driver, // Changed from $driver->user_id to $driver
            'Driver Application Approved',
            'Congratulations! Your driver application has been approved. You can now start accepting rides.'
        );

        return redirect()->back()->with('success', 'Driver approved successfully');
    }

    public function approveDocument($driverId, $documentId)
    {
        // Ambil document berdasarkan ID
        $document = DriverDocument::where('driver_id', $driverId)->where('id', $documentId)->firstOrFail();

        // Ubah statusnya jadi approved
        $document->status = 'approved';
        $document->save();

        // Redirect balik dengan flash message
        return redirect()->back()->with('success', 'Document approved successfully.');
    }

    public function rejectDocument($driverId, $documentId)
    {
        $document = DriverDocument::where('driver_id', $driverId)->where('id', $documentId)->firstOrFail();
        $document->status = 'rejected';
        $document->save();

        return redirect()->back()->with('success', 'Document rejected successfully.');
    }

    public function rejectDriver(Request $request, Driver $driver)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $driver->update([
            'status' => 'rejected',
            'is_verified' => false,
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Send notification
        $this->notificationService->sendDriverNotification(
            $driver->user_id,
            'Driver Application Rejected',
            'Your driver application has been rejected. Reason: ' . $request->rejection_reason
        );

        return redirect()->back()->with('success', 'Driver rejected successfully');
    }

    public function orders(Request $request)
    {
        $query = Order::with(['customer', 'driver']);

        // Search functionality
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'LIKE', "%{$search}%")
                  ->orWhere('pickup_address', 'LIKE', "%{$search}%")
                  ->orWhere('destination_address', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($customerQ) use ($search) {
                      $customerQ->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%")
                               ->orWhere('phone', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('driver.user', function($driverQ) use ($search) {
                      $driverQ->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Append query parameters to pagination links
        $orders->appends($request->query());

        return view('admin.orders.index', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $order->load(['customer', 'driver', 'tracking', 'ratings']);
        return view('admin.orders.show', compact('order'));
    }

    public function printOrderReceipt(Order $order)
    {
        $order->load(['customer', 'driver']);
        
        return view('admin.orders.receipt', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,driver_arrived,picked_up,in_progress,completed,cancelled'
        ]);

        try {
            DB::beginTransaction();

            // Update order status with timestamp
            $updateData = [
                'status' => $request->status,
                'updated_at' => now()
            ];

            // Add timestamp for specific status
            switch ($request->status) {
                case 'accepted':
                    $updateData['accepted_at'] = now();
                    break;
                case 'picked_up':
                    $updateData['picked_up_at'] = now();
                    break;
                case 'completed':
                    $updateData['completed_at'] = now();
                    break;
                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    break;
            }

            $order->update($updateData);

            // Add tracking entry
            $order->tracking()->create([
                'status' => $request->status,
                'notes' => 'Status updated by admin',
                'created_at' => now()
            ]);

            // Update driver status if needed
            if ($order->driver) {
                if ($request->status === 'completed' || $request->status === 'cancelled') {
                    $order->driver->update(['status' => 'available']);
                } elseif ($request->status === 'accepted') {
                    $order->driver->update(['status' => 'busy']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating order status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelOrder(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason,
                'cancelled_by' => 'admin'
            ]);

            // Add tracking entry
            $order->tracking()->create([
                'status' => 'cancelled',
                'notes' => 'Order cancelled by admin. Reason: ' . $request->reason,
                'created_at' => now()
            ]);

            // If driver was assigned, make them available again
            if ($order->driver) {
                $order->driver->update(['status' => 'available']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportOrder(Order $order)
    {
        // This is a placeholder - you can implement CSV/PDF export here
        return redirect()->back()->with('success', 'Export functionality will be implemented');
    }

    public function finances()
    {
        $stats = [
            'total_revenue' => Order::where('status', 'completed')->sum('fare_amount'),
            'total_commission' => Order::where('status', 'completed')->sum('platform_commission'),
            'driver_earnings' => Order::where('status', 'completed')->sum('driver_earning'),
            'pending_payouts' => Driver::sum('balance'),
            'today_revenue' => Order::where('status', 'completed')
                ->whereDate('created_at', Carbon::today())
                ->sum('fare_amount'),
            'this_month_revenue' => Order::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('fare_amount'),
        ];

        $top_drivers = Driver::with('user')
            ->withSum(['orders' => function ($query) {
                $query->where('status', 'completed');
            }], 'driver_earning')
            ->having('orders_sum_driver_earning', '>', 0)
            ->orderBy('orders_sum_driver_earning', 'desc')
            ->take(10)
            ->get();

        $recent_transactions = Order::with(['customer', 'driver'])
            ->where('status', 'completed')
            ->latest()
            ->take(15)
            ->get();

        return view('admin.finances.index', compact('stats', 'top_drivers', 'recent_transactions'));
    }

    public function vehicleTypes()
    {
        $vehicle_types = VehicleType::orderBy('name')->get();
        return view('admin.vehicle-types.index', compact('vehicle_types'));
    }

    public function createVehicleType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_types',
            'base_fare' => 'required|numeric|min:0',
            'per_km_rate' => 'required|numeric|min:0',
            'per_minute_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        VehicleType::create($request->all());

        return redirect()->route('admin.vehicle-types')
            ->with('success', 'Vehicle type created successfully');
    }

    public function updateVehicleType(Request $request, VehicleType $vehicleType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_types,name,' . $vehicleType->id,
            'base_fare' => 'required|numeric|min:0',
            'per_km_rate' => 'required|numeric|min:0',
            'per_minute_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $vehicleType->update($request->all());

        return redirect()->route('admin.vehicle-types')
            ->with('success', 'Vehicle type updated successfully');
    }

    public function deleteVehicleType(VehicleType $vehicleType)
    {
        if ($vehicleType->drivers()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete vehicle type that has associated drivers');
        }

        $vehicleType->delete();
        return redirect()->route('admin.vehicle-types')
            ->with('success', 'Vehicle type deleted successfully');
    }

    public function reports()
    {
        $date_range = [
            'start' => Carbon::now()->startOfMonth(),
            'end' => Carbon::now()->endOfMonth(),
        ];

        $order_stats = [
            'total_orders' => Order::whereBetween('created_at', [$date_range['start'], $date_range['end']])->count(),
            'completed_orders' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$date_range['start'], $date_range['end']])
                ->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')
                ->whereBetween('created_at', [$date_range['start'], $date_range['end']])
                ->count(),
        ];

        $financial_stats = [
            'revenue' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$date_range['start'], $date_range['end']])
                ->sum('fare_amount'),
            'commission' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$date_range['start'], $date_range['end']])
                ->sum('platform_commission'),
        ];

        $driver_stats = [
            'total_drivers' => Driver::count(),
            'active_drivers' => Driver::where('is_online', true)->count(),
            'new_drivers' => Driver::whereBetween('created_at', [$date_range['start'], $date_range['end']])->count(),
        ];

        return view('admin.reports.index', compact('order_stats', 'financial_stats', 'driver_stats', 'date_range'));
    }

    public function orderReports(Request $request)
    {
        $date_from = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $date_to = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = Order::with(['customer', 'driver']);

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(50);

        $stats = [
            'total_orders' => $query->count(),
            'completed_orders' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_orders' => (clone $query)->where('status', 'cancelled')->count(),
            'pending_orders' => (clone $query)->whereIn('status', ['pending', 'accepted', 'in_progress'])->count(),
            'total_revenue' => (clone $query)->where('status', 'completed')->sum('fare_amount'),
            'total_commission' => (clone $query)->where('status', 'completed')->sum('platform_commission'),
        ];

        $orders->appends($request->query());

        return view('admin.reports.orders', compact('orders', 'stats', 'date_from', 'date_to'));
    }

    public function driverReports(Request $request)
    {
        $date_from = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $date_to = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = Driver::with(['user', 'orders' => function($q) use ($date_from, $date_to) {
            $q->where('status', 'completed');
            if ($date_from) {
                $q->whereDate('created_at', '>=', $date_from);
            }
            if ($date_to) {
                $q->whereDate('created_at', '<=', $date_to);
            }
        }]);

        $drivers = $query->paginate(50);

        $stats = [
            'total_drivers' => Driver::count(),
            'active_drivers' => Driver::where('is_online', true)->count(),
            'verified_drivers' => Driver::where('is_verified', true)->count(),
            'new_drivers' => Driver::whereBetween('created_at', [$date_from, $date_to])->count(),
        ];

        $drivers->appends($request->query());

        return view('admin.reports.drivers', compact('drivers', 'stats', 'date_from', 'date_to'));
    }

    public function customerReports(Request $request)
    {
        $date_from = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $date_to = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = User::where('role', 'customer')->with(['orders' => function($q) use ($date_from, $date_to) {
            if ($date_from) {
                $q->whereDate('created_at', '>=', $date_from);
            }
            if ($date_to) {
                $q->whereDate('created_at', '<=', $date_to);
            }
        }]);

        $customers = $query->paginate(50);

        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'active_customers' => User::where('role', 'customer')->where('is_active', true)->count(),
            'new_customers' => User::where('role', 'customer')->whereBetween('created_at', [$date_from, $date_to])->count(),
            'customers_with_orders' => User::where('role', 'customer')->whereHas('orders')->count(),
        ];

        $customers->appends($request->query());

        return view('admin.reports.customers', compact('customers', 'stats', 'date_from', 'date_to'));
    }

    public function financialReports(Request $request)
    {
        $date_from = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $date_to = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $orders = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $date_from)
            ->whereDate('created_at', '<=', $date_to)
            ->with(['customer', 'driver'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total_revenue' => Order::where('status', 'completed')
                ->whereDate('created_at', '>=', $date_from)
                ->whereDate('created_at', '<=', $date_to)
                ->sum('fare_amount'),
            'total_commission' => Order::where('status', 'completed')
                ->whereDate('created_at', '>=', $date_from)
                ->whereDate('created_at', '<=', $date_to)
                ->sum('platform_commission'),
            'driver_earnings' => Order::where('status', 'completed')
                ->whereDate('created_at', '>=', $date_from)
                ->whereDate('created_at', '<=', $date_to)
                ->sum('driver_earning'),
            'average_order_value' => Order::where('status', 'completed')
                ->whereDate('created_at', '>=', $date_from)
                ->whereDate('created_at', '<=', $date_to)
                ->avg('fare_amount'),
        ];

        $orders->appends($request->query());

        return view('admin.reports.financial', compact('orders', 'stats', 'date_from', 'date_to'));
    }

    public function settings()
    {
        return view('admin.settings.index');
    }

    public function notifications()
    {
        return view('admin.notifications.index');
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:customer,driver,all',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($request->user_type === 'all') {
            $users = User::where('is_active', true)->get();
        } elseif ($request->user_type === 'customer') {
            $users = User::where('role', 'customer')->where('is_active', true)->get();
        } else {
            $users = User::where('role', 'driver')->where('is_active', true)->get();
        }

        if ($request->has('user_ids') && !empty($request->user_ids)) {
            $users = User::whereIn('id', $request->user_ids)->get();
        }

        foreach ($users as $user) {
            if ($user->role === 'driver') {
                $this->notificationService->sendDriverNotification(
                    $user->id,
                    $request->title,
                    $request->message
                );
            } else {
                $this->notificationService->sendCustomerNotification(
                    $user->id,
                    $request->title,
                    $request->message
                );
            }
        }

        return redirect()->back()->with('success', 'Notifications sent successfully to ' . $users->count() . ' users');
    }

    public function systemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
            'app_environment' => app()->environment(),
            'app_debug' => config('app.debug'),
            'app_timezone' => config('app.timezone'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        ];

        return view('admin.system-info', compact('info'));
    }
}
