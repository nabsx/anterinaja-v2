<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Driver;
use App\Services\OrderService;
use App\Services\FareCalculationService;
use App\Services\OSRMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    protected $orderService;
    protected $fareService;
    protected $osrmService;

    public function __construct(OrderService $orderService, FareCalculationService $fareService, OSRMService $osrmService)
    {
        $this->orderService = $orderService;
        $this->fareService = $fareService;
        $this->osrmService = $osrmService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get recent orders
        $recentOrders = Order::where('customer_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Get statistics
        $totalOrders = Order::where('customer_id', $user->id)->count();
        $completedOrders = Order::where('customer_id', $user->id)
            ->where('status', 'completed')->count();
        $activeOrders = Order::where('customer_id', $user->id)
            ->whereIn('status', ['pending', 'accepted', 'picking_up', 'in_progress'])
            ->count();

        return view('customer.dashboard', compact(
            'user', 'recentOrders', 'totalOrders', 'completedOrders', 'activeOrders'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
        ]);

        $user->update($request->only('name', 'phone', 'address', 'city'));

        return redirect()->route('customer.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('customer_id', $user->id)
            ->with('driver.user')
            ->latest()
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        $order->load('driver.user');
        return view('customer.order-detail', compact('order'));
    }

    public function bookRide()
    {
        return view('customer.book-ride');
    }

    /**
     * Calculate fare estimation (AJAX endpoint)
     */
    public function calculateFare(Request $request)
    {
        $request->validate([
            'pickup_latitude' => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'destination_latitude' => 'required|numeric|between:-90,90',
            'destination_longitude' => 'required|numeric|between:-180,180',
            'service_type' => 'required|in:motorcycle,car,van,truck',
        ]);

        try {
            // Step 1: Get distance and duration from OSRM
            $routeData = $this->osrmService->getDistanceAndDuration(
                $request->pickup_latitude,
                $request->pickup_longitude,
                $request->destination_latitude,
                $request->destination_longitude
            );

            if (!$routeData['success']) {
                throw new \Exception('Gagal mendapatkan rute: ' . ($routeData['error'] ?? 'Route tidak ditemukan'));
            }

            // Step 2: Calculate fare using the correct method name
            $fareData = $this->fareService->calculateFare(
                $routeData['distance_km'],
                $routeData['duration_minutes'],
                $request->service_type,
                $this->fareService->getCurrentConditions()
            );

            if (!$fareData['success']) {
                throw new \Exception('Gagal menghitung tarif: ' . ($fareData['error'] ?? 'Perhitungan gagal'));
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_fare' => $fareData['data']['total'],
                    'distance' => $routeData['distance_km'],
                    'duration' => $routeData['duration_minutes'],
                    'base_fare' => $fareData['data']['base_fare'],
                    'distance_fare' => $fareData['data']['distance_fare'],
                    'time_fare' => $fareData['data']['time_fare'],
                    'subtotal' => $fareData['data']['subtotal'],
                    'surcharges' => $fareData['data']['surcharges'],
                    'total_surcharge' => $fareData['data']['total_surcharge'],
                    'formatted_fare' => 'Rp ' . number_format($fareData['data']['total'], 0, ',', '.'),
                    'formatted_distance' => number_format($routeData['distance_km'], 2) . ' km',
                    'formatted_duration' => ceil($routeData['duration_minutes']) . ' menit',
                    'breakdown' => $fareData['data']['breakdown']
                ]
            ]);

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Fare calculation failed: ' . $e->getMessage(), [
                'pickup_lat' => $request->pickup_latitude,
                'pickup_lng' => $request->pickup_longitude,
                'dest_lat' => $request->destination_latitude,
                'dest_lng' => $request->destination_longitude,
                'service_type' => $request->service_type,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung tarif. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'pickup_address' => 'required|string',
            'pickup_latitude' => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'destination_address' => 'required|string',
            'destination_latitude' => 'required|numeric|between:-90,90',
            'destination_longitude' => 'required|numeric|between:-180,180',
            'service_type' => 'required|in:motorcycle,car,van,truck',
            'notes' => 'nullable|string|max:255',
        ]);
        
        try {
            // Use OrderService directly - it handles OSRM and fare calculation internally
            $result = $this->orderService->createOrder(Auth::id(), [
                'pickup_address' => $request->pickup_address,
                'pickup_lat' => $request->pickup_latitude,
                'pickup_lng' => $request->pickup_longitude,
                'destination_address' => $request->destination_address,
                'destination_lat' => $request->destination_latitude,
                'destination_lng' => $request->destination_longitude,
                'vehicle_type' => $request->service_type,
                'notes' => $request->notes,
            ]);

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            return redirect()->route('customer.orders.show', $result['data']->id)
                ->with('success', 'Pesanan berhasil dibuat! Mencari driver...');

        } catch (\Exception $e) {
            // Log error detail
            \Log::error('Create order failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'pickup_lat' => $request->pickup_latitude,
                'pickup_lng' => $request->pickup_longitude,
                'dest_lat' => $request->destination_latitude,
                'dest_lng' => $request->destination_longitude,
                'service_type' => $request->service_type,
                'error' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    public function cancelOrder(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'accepted'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan.');
        }

        $result = $this->orderService->cancelOrder($order->id, 'Dibatalkan oleh customer', 'customer');

        if ($result['success']) {
            return redirect()->route('customer.orders')
                ->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return back()->with('error', 'Gagal membatalkan pesanan.');
    }

    public function rateOrder(Request $request, Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);

        $this->orderService->rateOrder($order, $request->rating, $request->comment, 'customer');

        return back()->with('success', 'Rating berhasil diberikan.');
    }

    public function findDrivers(Request $request)
    {
        $latitude = $request->get('lat', -6.2088);
        $longitude = $request->get('lng', 106.8456);
        
        $drivers = Driver::with('user')
            ->verified()
            ->online()
            ->available()
            ->nearby($latitude, $longitude, 10)
            ->get();

        return view('customer.find-drivers', compact('drivers'));
    }
}