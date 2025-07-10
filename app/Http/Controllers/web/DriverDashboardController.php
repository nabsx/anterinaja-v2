<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\DriverDocument;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        return view('driver.dashboard', compact(
            'user', 'driver', 'recentOrders', 'totalOrders', 'completedOrders', 'activeOrders', 'todayEarnings'
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
        
        if (!$driver->is_verified) {
            return view('driver.available-orders')
                ->with('error', 'Akun Anda belum terverifikasi. Silakan lengkapi dokumen terlebih dahulu.');
        }

        $orders = Order::where('status', 'pending')
            ->where('service_type', $driver->vehicle_type)
            ->nearby($driver->current_latitude, $driver->current_longitude, 10)
            ->with('customer')
            ->latest()
            ->get();

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
            'status' => 'required|in:picking_up,in_progress,completed',
        ]);

        if ($order->driver_id !== Auth::user()->driver->id) {
            abort(403);
        }

        try {
            $this->orderService->updateOrderStatus($order, $request->status);
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
}
