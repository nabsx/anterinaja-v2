<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Order;
use App\Models\DriverDocument;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Get driver profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = $user->driver->load(['documents', 'ratings']);

            return response()->json([
                'success' => true,
                'data' => $driver
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get driver profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update driver location
     */
    public function updateLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = $user->driver;
            $driver->updateLocation($request->latitude, $request->longitude);

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully',
                'data' => [
                    'latitude' => $driver->current_latitude,
                    'longitude' => $driver->current_longitude,
                    'last_active_at' => $driver->last_active_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set driver online status
     */
    public function setOnlineStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_online' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = $user->driver;

            if (!$driver->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver must be verified to go online'
                ], 400);
            }

            if ($request->is_online) {
                $driver->setOnline();
                $message = 'Driver is now online';
            } else {
                $driver->setOffline();
                $message = 'Driver is now offline';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'is_online' => $driver->is_online,
                    'status' => $driver->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update online status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available orders for driver
     */
    public function availableOrders(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = $user->driver;

            if (!$driver->is_online || $driver->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver must be online and available'
                ], 400);
            }

            $radius = $request->query('radius', 5); // Default 5km radius

            // Get pending orders within radius
            $orders = Order::with(['customer'])
                ->where('status', 'pending')
                ->whereRaw(
                    "ST_Distance_Sphere(
                        point(pickup_longitude, pickup_latitude),
                        point(?, ?)
                    ) <= ?",
                    [$driver->current_longitude, $driver->current_latitude, $radius * 1000]
                )
                ->orderBy('created_at', 'asc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders,
                'count' => $orders->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get available orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept order
     */
    public function acceptOrder(Request $request, $orderId)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = $user->driver;

            if (!$driver->is_online || $driver->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver must be online and available'
                ], 400);
            }

            $result = $this->orderService->updateOrderStatus($orderId, 'accepted', $driver->id);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            // Set driver as busy
            $driver->setBusy();

            return response()->json([
                'success' => true,
                'message' => 'Order accepted successfully',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, $orderId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:driver_arrived,picked_up,in_progress,completed',
                'latitude' => 'sometimes|numeric|between:-90,90',
                'longitude' => 'sometimes|numeric|between:-180,180',
                'notes' => 'sometimes|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $additionalData = [];
            if ($request->has('notes')) {
                $additionalData['notes'] = $request->notes;
            }

            $result = $this->orderService->updateOrderStatus(
                $orderId, 
                $request->status, 
                $user->driver->id, 
                $additionalData
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            // Add tracking with location if provided
            if ($request->has('latitude') && $request->has('longitude')) {
                $order = $result['data'];
                $order->addTracking(
                    $request->status,
                    $request->notes,
                    $request->latitude,
                    $request->longitude
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload driver documents
     */
    public function uploadDocument(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|in:ktp,sim,stnk,photo',
                'document' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = $user->driver;

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
            $document = DriverDocument::updateOrCreate(
                [
                    'driver_id' => $driver->id,
                    'document_type' => $request->document_type
                ],
                [
                    'document_path' => $path
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'data' => $document
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver statistics
     */
    public function statistics(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $driver = $user->driver;

            // Get statistics
            $totalOrders = Order::where('driver_id', $driver->id)->count();
            $completedOrders = Order::where('driver_id', $driver->id)
                ->where('status', 'completed')->count();
            $cancelledOrders = Order::where('driver_id', $driver->id)
                ->where('status', 'cancelled')->count();
            
            $totalEarnings = Order::where('driver_id', $driver->id)
                ->where('status', 'completed')
                ->sum('driver_earning');

            $todayOrders = Order::where('driver_id', $driver->id)
                ->whereDate('created_at', today())
                ->count();

            $todayEarnings = Order::where('driver_id', $driver->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', today())
                ->sum('driver_earning');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_orders' => $totalOrders,
                    'completed_orders' => $completedOrders,
                    'cancelled_orders' => $cancelledOrders,
                    'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
                    'total_earnings' => $totalEarnings,
                    'today_orders' => $todayOrders,
                    'today_earnings' => $todayEarnings,
                    'rating' => $driver->rating,
                    'total_trips' => $driver->total_trips,
                    'is_verified' => $driver->is_verified,
                    'is_online' => $driver->is_online,
                    'status' => $driver->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}