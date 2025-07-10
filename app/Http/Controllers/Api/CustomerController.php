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
     * Get customer profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Customer role required.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $user->load('orders')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get customer profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate fare estimate
     */
    public function calculateFare(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pickup_lat' => 'required|numeric|between:-90,90',
                'pickup_lng' => 'required|numeric|between:-180,180',
                'destination_lat' => 'required|numeric|between:-90,90',
                'destination_lng' => 'required|numeric|between:-180,180',
                'vehicle_type' => 'sometimes|in:motorcycle,car,van,truck',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get route data
            $routeData = $this->osrmService->getDistanceAndDuration(
                $request->pickup_lat,
                $request->pickup_lng,
                $request->destination_lat,
                $request->destination_lng
            );

            if (!$routeData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate route'
                ], 400);
            }

            // Calculate fare
            $vehicleType = $request->vehicle_type ?? 'car';
            $conditions = $this->fareCalculationService->getCurrentConditions();

            $fareData = $this->fareCalculationService->calculateFare(
                $routeData['distance_km'],
                $routeData['duration_minutes'],
                $vehicleType,
                $conditions
            );

            if (!$fareData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate fare'
                ], 400);
            }

            // Add commission (10%)
            $driverFare = $fareData['data']['total'];
            $commission = round($driverFare * 0.10);
            $customerFare = $driverFare + $commission;

            return response()->json([
                'success' => true,
                'data' => [
                    'distance_km' => $routeData['distance_km'],
                    'duration_minutes' => $routeData['duration_minutes'],
                    'vehicle_type' => $vehicleType,
                    'fare_breakdown' => [
                        'base' => $fareData['data']['base'],
                        'distance' => $fareData['data']['distance'],
                        'duration' => $fareData['data']['duration'],
                        'driver_total' => $driverFare,
                        'commission' => $commission,
                        'customer_total' => $customerFare,
                    ],
                    'estimated_fare' => $customerFare
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate fare',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find nearby drivers
     */
    public function findNearbyDrivers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'vehicle_type' => 'sometimes|in:motorcycle,car,van,truck',
                'radius' => 'sometimes|numeric|min:1|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $vehicleType = $request->vehicle_type ?? 'car';
            $radius = $request->radius ?? 10;

            $drivers = Driver::select('drivers.*')
                ->selectRaw('
                    ( 6371 * acos( cos( radians(?) ) * 
                    cos( radians( drivers.current_latitude ) ) * 
                    cos( radians( drivers.current_longitude ) - radians(?) ) + 
                    sin( radians(?) ) * 
                    sin( radians( drivers.current_latitude ) ) ) ) AS distance
                ', [$request->latitude, $request->longitude, $request->latitude])
                ->where('is_online', true)
                ->where('status', 'available')
                ->where('vehicle_type', $vehicleType)
                ->where('is_verified', true)
                ->having('distance', '<=', $radius)
                ->orderBy('distance', 'asc')
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
                'message' => 'Failed to find nearby drivers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer order statistics
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

            $totalOrders = Order::where('user_id', $user->id)->count();
            $completedOrders = Order::where('user_id', $user->id)
                ->where('status', 'completed')->count();
            $cancelledOrders = Order::where('user_id', $user->id)
                ->where('status', 'cancelled')->count();

            $totalSpent = Order::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('estimated_fare');

            $thisMonthOrders = Order::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $thisMonthSpent = Order::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->sum('estimated_fare');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_orders' => $totalOrders,
                    'completed_orders' => $completedOrders,
                    'cancelled_orders' => $cancelledOrders,
                    'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
                    'total_spent' => $totalSpent,
                    'this_month_orders' => $thisMonthOrders,
                    'this_month_spent' => $thisMonthSpent,
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