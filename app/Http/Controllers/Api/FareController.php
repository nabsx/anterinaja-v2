<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FareCalculationService;
use App\Services\OSRMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FareController extends Controller
{
    protected $fareCalculationService;
    protected $osrmService;

    public function __construct(FareCalculationService $fareCalculationService, OSRMService $osrmService)
    {
        $this->fareCalculationService = $fareCalculationService;
        $this->osrmService = $osrmService;
    }

    /**
     * Calculate fare estimate (public endpoint)
     */
    public function estimate(Request $request)
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

            // Get route data from OSRM
            $routeData = $this->osrmService->getDistanceAndDuration(
                $request->pickup_lat,
                $request->pickup_lng,
                $request->destination_lat,
                $request->destination_lng
            );

            if (!$routeData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate route',
                    'error' => $routeData['error'] ?? 'Route calculation failed'
                ], 400);
            }

            $vehicleType = $request->vehicle_type ?? 'car';
            $conditions = $this->fareCalculationService->getCurrentConditions();

            // Calculate fare
            $fareData = $this->fareCalculationService->calculateFare(
                $routeData['distance_km'],
                $routeData['duration_minutes'],
                $vehicleType,
                $conditions
            );

            if (!$fareData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate fare',
                    'error' => $fareData['error'] ?? 'Fare calculation failed'
                ], 400);
            }

            // Add platform commission (10%)
            $driverFare = $fareData['data']['total'];
            $commission = round($driverFare * 0.10);
            $customerFare = $driverFare + $commission;

            return response()->json([
                'success' => true,
                'data' => [
                    'route' => [
                        'distance_km' => $routeData['distance_km'],
                        'duration_minutes' => $routeData['duration_minutes'],
                        'polyline' => $routeData['polyline'] ?? null,
                    ],
                    'vehicle_type' => $vehicleType,
                    'fare_breakdown' => [
                        'base_fare' => $fareData['data']['base'],
                        'distance_fare' => $fareData['data']['distance'],
                        'time_fare' => $fareData['data']['duration'],
                        'surge_multiplier' => $fareData['data']['surge_multiplier'] ?? 1,
                        'driver_total' => $driverFare,
                        'platform_commission' => $commission,
                        'customer_total' => $customerFare,
                    ],
                    'estimated_fare' => $customerFare,
                    'conditions' => $conditions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate fare estimate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fare rates for all vehicle types
     */
    public function rates(Request $request)
    {
        try {
            $rates = [
                'motorcycle' => [
                    'base_fare' => 5000,
                    'per_km' => 2000,
                    'per_minute' => 300,
                    'minimum_fare' => 8000,
                ],
                'car' => [
                    'base_fare' => 8000,
                    'per_km' => 3000,
                    'per_minute' => 400,
                    'minimum_fare' => 12000,
                ],
                'van' => [
                    'base_fare' => 12000,
                    'per_km' => 4000,
                    'per_minute' => 500,
                    'minimum_fare' => 18000,
                ],
                'truck' => [
                    'base_fare' => 20000,
                    'per_km' => 5000,
                    'per_minute' => 600,
                    'minimum_fare' => 30000,
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $rates,
                'message' => 'Fare rates retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get fare rates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current surge pricing information
     */
    public function surge(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pickup_lat' => 'required|numeric|between:-90,90',
                'pickup_lng' => 'required|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $conditions = $this->fareCalculationService->getCurrentConditions();

            // Calculate surge multiplier based on various factors
            $surgeMultiplier = 1.0;

            if ($conditions['is_night']) {
                $surgeMultiplier += 0.3; // 30% increase at night
            }

            if ($conditions['is_peak_hour'] ?? false) {
                $surgeMultiplier += 0.5; // 50% increase during peak hours
            }

            if ($conditions['is_raining'] ?? false) {
                $surgeMultiplier += 0.2; // 20% increase when raining
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'surge_multiplier' => $surgeMultiplier,
                    'conditions' => $conditions,
                    'message' => $surgeMultiplier > 1.0 ? 'Surge pricing is active' : 'Normal pricing'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get surge information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}