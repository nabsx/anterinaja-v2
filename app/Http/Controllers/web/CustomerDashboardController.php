<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Driver;
use App\Services\OrderService;
use App\Services\FareCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    protected $orderService;
    protected $fareService;

    public function __construct(OrderService $orderService, FareCalculationService $fareService)
    {
        $this->orderService = $orderService;
        $this->fareService = $fareService;
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

    public function createOrder(Request $request)
    {
        $request->validate([
            'pickup_address' => 'required|string',
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'destination_address' => 'required|string',
            'destination_latitude' => 'required|numeric',
            'destination_longitude' => 'required|numeric',
            'service_type' => 'required|in:motorcycle,car',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $fare = $this->fareService->calculate(
                $request->pickup_latitude,
                $request->pickup_longitude,
                $request->destination_latitude,
                $request->destination_longitude,
                $request->service_type
            );

            $order = $this->orderService->createOrder([
                'customer_id' => Auth::id(),
                'pickup_address' => $request->pickup_address,
                'pickup_latitude' => $request->pickup_latitude,
                'pickup_longitude' => $request->pickup_longitude,
                'destination_address' => $request->destination_address,
                'destination_latitude' => $request->destination_latitude,
                'destination_longitude' => $request->destination_longitude,
                'service_type' => $request->service_type,
                'estimated_fare' => $fare['total_fare'],
                'distance' => $fare['distance'],
                'estimated_duration' => $fare['duration'],
                'notes' => $request->notes,
            ]);

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat! Mencari driver...');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
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

        $this->orderService->cancelOrder($order, 'Dibatalkan oleh customer');

        return redirect()->route('customer.orders')
            ->with('success', 'Pesanan berhasil dibatalkan.');
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
