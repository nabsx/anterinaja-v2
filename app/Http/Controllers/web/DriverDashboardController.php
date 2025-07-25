<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\DriverDocument;
use App\Models\Rating;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DriverDashboardController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $user = Auth::user();
        $driver = $user->driver;

        // Get recent orders
        $recentOrders = Order::where('driver_id', $driver->id)
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        // Get statistics
        $totalOrders = Order::where('driver_id', $driver->id)->count();
        $completedOrders = Order::where('driver_id', $driver->id)
            ->where('status', 'completed')->count();
        $activeOrders = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'picking_up', 'in_progress'])
            ->count();
        $todayEarnings = Order::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->sum('driver_earning');

        // Get rating statistics
        $averageRating = Rating::where('driver_id', $driver->id)
            ->where('rated_by', 'customer')
            ->avg('rating') ?? 0;
        
        $totalRatings = Rating::where('driver_id', $driver->id)
            ->where('rated_by', 'customer')
            ->count();

        return view('driver.dashboard', compact(
            'user', 'driver', 'recentOrders', 'totalOrders', 'completedOrders', 
            'activeOrders', 'todayEarnings', 'averageRating', 'totalRatings'
        ));
    }

    public function ratings()
    {
        $driver = Auth::user()->driver;

        // Get all ratings for this driver (anonymous)
        $ratings = Rating::where('driver_id', $driver->id)
            ->where('rated_by', 'customer')
            ->with(['order' => function($query) {
                $query->select('id', 'order_code', 'distance_km', 'vehicle_type');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate statistics
        $averageRating = Rating::where('driver_id', $driver->id)
            ->where('rated_by', 'customer')
            ->avg('rating') ?? 0;

        $totalRatings = Rating::where('driver_id', $driver->id)
            ->where('rated_by', 'customer')
            ->count();

        // Get rating distribution
        $ratingStats = Rating::where('driver_id', $driver->id)
            ->where('rated_by', 'customer')
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return view('driver.ratings', compact(
            'ratings', 'averageRating', 'totalRatings', 'ratingStats'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        $driver = $user->driver;
        return view('driver.profile', compact('user', 'driver'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $driver = $user->driver;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'vehicle_brand' => 'required|string|max:100',
            'vehicle_model' => 'required|string|max:100',
            'vehicle_year' => 'required|integer|min:1990|max:' . date('Y'),
            'vehicle_plate' => 'required|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('name', 'phone', 'address', 'city'));
        $driver->update($request->only(
            'vehicle_brand', 'vehicle_model', 'vehicle_year', 'vehicle_plate',
            'emergency_contact_name', 'emergency_contact_phone'
        ));

        return redirect()->route('driver.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('driver_id', $user->driver->id)
            ->with('customer')
            ->latest()
            ->paginate(10);

        return view('driver.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        if ($order->driver_id !== Auth::user()->driver->id) {
            abort(403);
        }

        $order->load('customer');
        return view('driver.order-detail', compact('order'));
    }

        public function availableOrders()
    {
        $driver = Auth::user()->driver;

        // Initialize empty orders collection
        $orders = collect();

        if (!$driver->is_verified) {
            return view('driver.available-orders', compact('orders'))
                ->with('error', 'Akun Anda belum terverifikasi. Silakan lengkapi dokumen terlebih dahulu.');
        }

        if ($driver->current_latitude && $driver->current_longitude) {
            // Get all pending orders for the driver's vehicle type
            $allOrders = Order::where('status', 'pending')
                ->where('vehicle_type', $driver->vehicle_type)
                ->with('customer')
                ->latest()
                ->get();

            // Filter orders within 10km radius
            $orders = $allOrders->filter(function ($order) use ($driver) {
                $distance = $this->calculateDistance(
                    $driver->current_latitude,
                    $driver->current_longitude,
                    $order->pickup_latitude,
                    $order->pickup_longitude
                );
                
                // Add distance to order object for display
                $order->distance_from_driver = $distance;
                
                return $distance <= 10; // 10km radius
            })->sortBy('distance_from_driver');
        } else {
            // If driver location is not available, show all pending orders for their vehicle type
            $orders = Order::where('status', 'pending')
                ->where('vehicle_type', $driver->vehicle_type)
                ->with('customer')
                ->latest()
                ->get();
            
            // Add distance_from_driver property even if we can't calculate it
            $orders->each(function ($order) {
                $order->distance_from_driver = null;
            });
        }

        return view('driver.available-orders', compact('orders'));
    }

    public function acceptOrder(Order $order)
    {
        $driver = Auth::user()->driver;

        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan sudah tidak tersedia.');
        }

        try {
            $this->orderService->acceptOrder($order, $driver);
            return redirect()->route('driver.orders.show', $order)
                ->with('success', 'Pesanan berhasil diterima!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menerima pesanan. ' . $e->getMessage());
        }
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:driver_arrived,picked_up,in_progress,completed',
        ]);

        if ($order->driver_id !== Auth::user()->driver->id) {
            abort(403);
        }

        try {
            $result = $this->orderService->updateOrderStatus(
                $order->id, 
                $request->status, 
                Auth::user()->driver->id
            );
        
            if (!$result['success']) {
                return back()->with('error', $result['error']);
            }
        
            return back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status. ' . $e->getMessage());
        }
    }

    public function earnings()
    {
        $driver = Auth::user()->driver;

        $todayEarnings = Order::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->sum('driver_earning');

        $thisWeekEarnings = Order::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('driver_earning');

        $thisMonthEarnings = Order::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->sum('driver_earning');

        $totalEarnings = Order::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->sum('driver_earning');

        return view('driver.earnings', compact(
            'todayEarnings', 'thisWeekEarnings', 'thisMonthEarnings', 'totalEarnings', 'driver'
        ));
    }

    public function documents()
    {
        $driver = Auth::user()->driver;
        $documents = DriverDocument::where('driver_id', $driver->id)->get();

        return view('driver.documents', compact('driver', 'documents'));
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:ktp,sim,stnk,photo',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $driver = Auth::user()->driver;

        // Check if document already exists
        $existingDocument = DriverDocument::where('driver_id', $driver->id)
            ->where('document_type', $request->document_type)
            ->first();

        // Delete old document file if exists
        if ($existingDocument && $existingDocument->document_path) {
            Storage::disk('public')->delete($existingDocument->document_path);
        }

        // Upload new document
        $path = $request->file('document')->store('driver_documents', 'public');

        // Create or update document record
        DriverDocument::updateOrCreate(
            [
                'driver_id' => $driver->id,
                'document_type' => $request->document_type
            ],
            [
                'document_path' => $path
            ]
        );

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'is_online' => 'required|boolean',
        ]);

        $driver = Auth::user()->driver;

        if ($request->is_online) {
            $driver->setOnline();
            $message = 'Status berhasil diubah ke online.';
        } else {
            $driver->setOffline();
            $message = 'Status berhasil diubah ke offline.';
        }

        return back()->with('success', $message);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $driver = Auth::user()->driver;
        $driver->updateLocation($request->latitude, $request->longitude);

        return response()->json(['success' => true]);
    }

    public function debugOrders(Request $request)
    {
        try {
            $user = $request->user();
            $driver = $user->driver;

            // Get all pending orders
            $allPendingOrders = Order::where('status', 'pending')->get();
        
            // Get driver info
            $driverInfo = [
                'id' => $driver->id,
                'is_online' => $driver->is_online,
                'status' => $driver->status,
                'current_latitude' => $driver->current_latitude,
                'current_longitude' => $driver->current_longitude,
                'last_active_at' => $driver->last_active_at,
            ];

            // Calculate distances for all pending orders
            $ordersWithDistance = $allPendingOrders->map(function ($order) use ($driver) {
                $distance = null;
                if ($driver->current_latitude && $driver->current_longitude) {
                    $distance = $this->calculateDistance(
                        $driver->current_latitude,
                        $driver->current_longitude,
                        $order->pickup_latitude,
                        $order->pickup_longitude
                    );
                }
            
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'pickup_address' => $order->pickup_address,
                    'pickup_latitude' => $order->pickup_latitude,
                    'pickup_longitude' => $order->pickup_longitude,
                    'status' => $order->status,
                    'distance_km' => $distance,
                    'created_at' => $order->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'driver' => $driverInfo,
                    'total_pending_orders' => $allPendingOrders->count(),
                    'orders_with_distance' => $ordersWithDistance,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debug failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
