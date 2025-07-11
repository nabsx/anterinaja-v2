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
            'pending_orders' => Order::whereIn('status', ['pending', 'accepted', 'on_the_way'])->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('platform_commission'),
            'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
            'pending_driver_approvals' => Driver::where('status', 'pending')->count(),
            'pending_documents' => DriverDocument::where('status', 'pending')->count(),
        ];

        $recent_orders = Order::with(['customer', 'driver'])
            ->latest()
            ->take(10)
            ->get();

        $pending_drivers = Driver::with('user')
            ->where('status', 'pending')
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

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->get('role') !== '') {
            $query->where('role', $request->get('role'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function userDetail(User $user)
    {
        $user->load(['orders', 'driver']);
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

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('license_number', 'like', "%{$search}%")
              ->orWhere('vehicle_plate', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('vehicle_type') && $request->get('vehicle_type') !== '') {
            $query->where('vehicle_type_id', $request->get('vehicle_type'));
        }

        $drivers = $query->orderBy('created_at', 'desc')->paginate(20);
        $vehicle_types = VehicleType::all();

        return view('admin.drivers.index', compact('drivers', 'vehicle_types'));
    }

    public function driverDetail(Driver $driver)
    {
        $driver->load(['user', 'vehicleType', 'orders', 'documents']);
        return view('admin.drivers.show', compact('driver'));
    }

    public function approveDriver(Driver $driver)
    {
        $driver->update(['status' => 'approved']);
        
        $this->notificationService->sendDriverNotification(
            $driver->user_id,
            'Driver Application Approved',
            'Congratulations! Your driver application has been approved. You can now start accepting rides.'
        );

        return redirect()->back()->with('success', 'Driver approved successfully');
    }

    public function rejectDriver(Request $request, Driver $driver)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $driver->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

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

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('pickup_address', 'like', "%{$search}%")
                  ->orWhere('delivery_address', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQ) use ($search) {
                      $customerQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('date_from') && $request->get('date_from') !== '') {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to') && $request->get('date_to') !== '') {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $order->load(['customer', 'driver', 'tracking', 'rating']);
        return view('admin.orders.show', compact('order'));
    }

    public function finances()
    {
        $stats = [
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'total_commission' => Order::where('status', 'completed')->sum('commission'),
            'driver_earnings' => Order::where('status', 'completed')->sum('driver_earning'),
            'pending_payouts' => Driver::sum('balance'),
            'today_revenue' => Order::where('status', 'completed')
                ->whereDate('created_at', Carbon::today())
                ->sum('total_amount'),
            'this_month_revenue' => Order::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('total_amount'),
        ];

        $top_drivers = Driver::with('user')
            ->where('total_earnings', '>', 0)
            ->orderBy('total_earnings', 'desc')
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
                ->sum('total_amount'),
            'commission' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$date_range['start'], $date_range['end']])
                ->sum('commission'),
        ];

        $driver_stats = [
            'total_drivers' => Driver::count(),
            'active_drivers' => Driver::where('is_online', true)->count(),
            'new_drivers' => Driver::whereBetween('created_at', [$date_range['start'], $date_range['end']])->count(),
        ];

        return view('admin.reports.index', compact('order_stats', 'financial_stats', 'driver_stats', 'date_range'));
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