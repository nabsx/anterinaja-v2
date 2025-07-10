<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Driver;
use App\Services\FareCalculationService;
use App\Services\OSRMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    protected $fareCalculationService;
    protected $osrmService;

    public function __construct(FareCalculationService $fareCalculationService, OSRMService $osrmService)
    {
        $this->fareCalculationService = $fareCalculationService;
        $this->osrmService = $osrmService;
    }

    /**
     * Get customer statistics
     */
    public function statistics(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Customer role required.'
                ], 403);
            }

            // Get statistics
            $totalOrders = Order::where('customer_id', $user->id)->count();
            $completedOrders = Order::where('customer_id', $user->id)
                ->where('status', 'completed')->count();
            $cancelledOrders = Order::where('customer_id', $user->id)
                ->where('status', 'cancelled')->count();
            
            $totalSpent = Order::where('customer_id', $user->id)
                ->where('status', 'completed')
                ->sum('fare_amount');

            $rideOrders = Order::where('customer_id', $user->id)
                ->where('order_type', 'ride')->count();
            $deliveryOrders = Order::where('customer_id', $user->id)
                ->where('order_type', 'delivery')->count();

            $thisMonthOrders = Order::where('customer_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $thisMonthSpent = Order::where('customer_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->sum('fare_amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_orders' => $totalOrders,
                    'completed_orders' => $completedOrders,
                    'cancelled_orders' => $cancelledOrders,
                    'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
                    'total_spent' => $totalSpent,
                    'ride_orders' => $rideOrders,
                    'delivery_orders' => $deliveryOrders,
                    'this_month_orders' => $thisMonthOrders,
                    'this_month_spent' => $thisMonthSpent
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

    /**
     * Get nearby drivers
     */
    public function nearbyDrivers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'vehicle_type' => 'sometimes|in:motorcycle,car',
                'radius' => 'sometimes|numeric|min:1|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $vehicleType = $request->vehicle_type;
            $radius = $request->radius ?? 5; // Default 5km

            $query = Driver::select('drivers.*')
                ->selectRaw('
                    ( 6371 * acos( cos( radians(?) ) * 
                    cos( radians( drivers.current_latitude ) ) * 
                    cos( radians( drivers.current_longitude ) - radians(?) ) + 
                    sin( radians(?) ) * 
                    sin( radians( drivers.current_latitude ) ) ) ) AS distance
                ', [$latitude, $longitude, $latitude])
                ->with(['user'])
                ->where('is_verified', true)
                ->where('is_online', true)
                ->where('status', 'available')
                ->whereNotNull('current_latitude')
                ->whereNotNull('current_longitude')
                ->having('distance', '<=', $radius);

            if ($vehicleType) {
                $query->where('vehicle_type', $vehicleType);
            }

            $drivers = $query->orderBy('distance', 'asc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $drivers,
                'count' => $drivers->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get nearby drivers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order history with filters
     */
    public function orderHistory(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Customer role required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|in:pending,accepted,driver_arrived,picked_up,in_progress,completed,cancelled',
                'order_type' => 'sometimes|in:ride,delivery',
                'limit' => 'sometimes|integer|min:1|max:100',
                'page' => 'sometimes|integer|min:1',
                'date_from' => 'sometimes|date',
                'date_to' => 'sometimes|date|after_or_equal:date_from',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Order::with(['driver.user', 'delivery', 'ratings'])
                ->where('customer_id', $user->id);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('order_type')) {
                $query->where('order_type', $request->order_type);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $limit = $request->limit ?? 20;
            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'has_more' => $orders->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get order history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get favorite locations (most used pickup/destination addresses)
     */
    public function favoriteLocations(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Customer role required.'
                ], 403);
            }

            // Get most used pickup locations
            $pickupLocations = Order::where('customer_id', $user->id)
                ->select('pickup_address', 'pickup_latitude', 'pickup_longitude')
                ->selectRaw('COUNT(*) as usage_count')
                ->groupBy('pickup_address', 'pickup_latitude', 'pickup_longitude')
                ->orderBy('usage_count', 'desc')
                ->limit(5)
                ->get();

            // Get most used destination locations
            $destinationLocations = Order::where('customer_id', $user->id)
                ->select('destination_address', 'destination_latitude', 'destination_longitude')
                ->selectRaw('COUNT(*) as usage_count')
                ->groupBy('destination_address', 'destination_latitude', 'destination_longitude')
                ->orderBy('usage_count', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'pickup_locations' => $pickupLocations,
                    'destination_locations' => $destinationLocations
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get favorite locations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track active order
     */
    public function trackOrder(Request $request, $orderId)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Customer role required.'
                ], 403);
            }

            $order = Order::with(['driver.user', 'trackings'])
                ->where('id', $orderId)
                ->where('customer_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Only track active orders
            if (in_array($order->status, ['completed', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot track completed or cancelled orders'
                ], 400);
            }

            $trackingData = [
                'order' => $order,
                'driver_location' => null,
                'estimated_arrival' => null
            ];

            // Get driver's current location if order is accepted
            if ($order->driver && in_array($order->status, ['accepted', 'driver_arrived', 'picked_up', 'in_progress'])) {
                $driver = $order->driver;
                $trackingData['driver_location'] = [
                    'latitude' => $driver->current_latitude,
                    'longitude' => $driver->current_longitude,
                    'last_updated' => $driver->last_active_at
                ];

                // Calculate estimated arrival time if driver is on the way
                if (in_array($order->status, ['accepted', 'driver_arrived'])) {
                    $routeData = $this->osrmService->getDistanceAndDuration(
                        $driver->current_latitude,
                        $driver->current_longitude,
                        $order->pickup_latitude,
                        $order->pickup_longitude
                    );

                    if ($routeData['success']) {
                        $trackingData['estimated_arrival'] = [
                            'distance_km' => $routeData['distance_km'],
                            'duration_minutes' => $routeData['duration_minutes'],
                            'estimated_time' => now()->addMinutes($routeData['duration_minutes'])
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $trackingData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}