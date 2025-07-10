<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FareCalculationService;
use App\Services\OSRMService;
use App\Models\VehicleType;
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
     * Calculate fare estimate (public endpoint - no auth required)
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

            // Get route information
            $routeData = $this->osrmService->getDistanceAndDuration(
                $request->pickup_lat,
                $request->pickup_lng,
                $request->destination_lat,
                $request->destination_lng
            );

            if (!$routeData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate route. Please check the coordinates.'
                ], 400);
            }

            $vehicleType = $request->vehicle_type ?? 'car';
            
            // Get current conditions (time-based surcharges)
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
                    'message' => 'Unable to calculate fare'
                ], 400);
            }

            // Calculate final fare with commission
            $finalFareData = $this->fareCalculationService->calculateFinalFare(
                $fareData['data']['total']
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'route' => [
                        'distance_km' => $routeData['distance_km'],
                        'distance_meters' => $routeData['distance'],
                        'duration_minutes' => $routeData['duration_minutes'],
                        'duration_seconds' => $routeData['duration']
                    ],
                    'fare' => [
                        'base_fare' => $fareData['data']['base_fare'],
                        'distance_fare' => $fareData['data']['distance_fare'],
                        'time_fare' => $fareData['data']['time_fare'],
                        'subtotal' => $fareData['data']['subtotal'],
                        'surcharges' => $fareData['data']['surcharges'],
                        'total_surcharge' => $fareData['data']['total_surcharge'],
                        'driver_earning' => $fareData['data']['total'],
                        'commission' => $finalFareData['commission'],
                        'customer_fare' => $finalFareData['final_fare'],
                        'currency' => 'IDR'
                    ],
                    'vehicle_type' => $vehicleType,
                    'conditions' => $conditions,
                    'breakdown' => $fareData['data']['breakdown']
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
     * Get fare estimates for all vehicle types
     */
    public function estimateAll(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pickup_lat' => 'required|numeric|between:-90,90',
                'pickup_lng' => 'required|numeric|between:-180,180',
                'destination_lat' => 'required|numeric|between:-90,90',
                'destination_lng' => 'required|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get route information
            $routeData = $this->osrmService->getDistanceAndDuration(
                $request->pickup_lat,
                $request->pickup_lng,
                $request->destination_lat,
                $request->destination_lng
            );

            if (!$routeData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate route. Please check the coordinates.'
                ], 400);
            }

            // Get active vehicle types
            $vehicleTypes = VehicleType::where('is_active', true)->get();
            $conditions = $this->fareCalculationService->getCurrentConditions();
            $estimates = [];

            foreach ($vehicleTypes as $vehicleType) {
                $fareData = $this->fareCalculationService->calculateFare(
                    $routeData['distance_km'],
                    $routeData['duration_minutes'],
                    $vehicleType->name,
                    $conditions
                );

                if ($fareData['success']) {
                    $finalFareData = $this->fareCalculationService->calculateFinalFare(
                        $fareData['data']['total']
                    );

                    $estimates[] = [
                        'vehicle_type' => $vehicleType->name,
                        'display_name' => $vehicleType->display_name,
                        'capacity' => $vehicleType->capacity,
                        'icon' => $vehicleType->icon_url,
                        'fare' => [
                            'driver_earning' => $fareData['data']['total'],
                            'commission' => $finalFareData['commission'],
                            'customer_fare' => $finalFareData['final_fare'],
                            'currency' => 'IDR'
                        ],
                        'estimated_time' => $routeData['duration_minutes'] . ' minutes'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'route' => [
                        'distance_km' => $routeData['distance_km'],
                        'duration_minutes' => $routeData['duration_minutes']
                    ],
                    'estimates' => $estimates,
                    'conditions' => $conditions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate fare estimates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle types
     */
    public function vehicleTypes()
    {
        try {
            $vehicleTypes = VehicleType::where('is_active', true)
                ->orderBy('base_fare', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $vehicleTypes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get vehicle types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fare breakdown details
     */
    public function breakdown(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pickup_lat' => 'required|numeric|between:-90,90',
                'pickup_lng' => 'required|numeric|between:-180,180',
                'destination_lat' => 'required|numeric|between:-90,90',
                'destination_lng' => 'required|numeric|between:-180,180',
                'vehicle_type' => 'required|in:motorcycle,car,van,truck',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get route information
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

            $conditions = $this->fareCalculationService->getCurrentConditions();

            // Calculate detailed fare
            $fareData = $this->fareCalculationService->calculateFare(
                $routeData['distance_km'],
                $routeData['duration_minutes'],
                $request->vehicle_type,
                $conditions
            );

            if (!$fareData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate fare'
                ], 400);
            }

            $finalFareData = $this->fareCalculationService->calculateFinalFare(
                $fareData['data']['total']
            );

            // Get vehicle type details
            $vehicleType = VehicleType::where('name', $request->vehicle_type)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'vehicle' => [
                        'type' => $vehicleType->name,
                        'display_name' => $vehicleType->display_name,
                        'capacity' => $vehicleType->capacity
                    ],
                    'route' => [
                        'distance_km' => $routeData['distance_km'],
                        'duration_minutes' => $routeData['duration_minutes']
                    ],
                    'fare_breakdown' => [
                        'base_fare' => [
                            'amount' => $fareData['data']['base_fare'],
                            'description' => 'Base fare for ' . $vehicleType->display_name
                        ],
                        'distance_fare' => [
                            'amount' => $fareData['data']['distance_fare'],
                            'description' => $routeData['distance_km'] . ' km × Rp ' . number_format($fareData['data']['breakdown']['per_km_rate'])
                        ],
                        'time_fare' => [
                            'amount' => $fareData['data']['time_fare'],
                            'description' => $routeData['duration_minutes'] . ' minutes × Rp ' . number_format($fareData['data']['breakdown']['per_minute_rate'])
                        ],
                        'subtotal' => $fareData['data']['subtotal'],
                        'surcharges' => $fareData['data']['surcharges'],
                        'driver_earning' => $fareData['data']['total'],
                        'platform_commission' => [
                            'amount' => $finalFareData['commission'],
                            'percentage' => '10%',
                            'description' => 'Platform service fee'
                        ],
                        'total_customer_fare' => $finalFareData['final_fare']
                    ],
                    'conditions' => $conditions,
                    'currency' => 'IDR'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get fare breakdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}