<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OSRMService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.osrm.base_url', 'https://router.project-osrm.org');
        $this->timeout = config('services.osrm.timeout', 10);
    }

    /**
     * Get distance and duration between two points
     */
    public function getDistanceAndDuration($pickupLat, $pickupLng, $destinationLat, $destinationLng)
    {
        try {
            $url = "{$this->baseUrl}/route/v1/driving/{$pickupLng},{$pickupLat};{$destinationLng},{$destinationLat}";
            
            $response = Http::timeout($this->timeout)->get($url, [
                'overview' => 'full',
                'geometries' => 'polyline'
            ]);

            if (!$response->successful()) {
                throw new \Exception('OSRM API request failed');
            }

            $data = $response->json();

            if (!isset($data['routes'][0])) {
                throw new \Exception('No route found');
            }

            $route = $data['routes'][0];
            $distance = $route['distance']; // in meters
            $duration = $route['duration']; // in seconds

            return [
                'success' => true,
                'distance_km' => round($distance / 1000, 2),
                'distance_m' => $distance,
                'duration_minutes' => round($duration / 60, 2),
                'duration_seconds' => $duration,
                'polyline' => $route['geometry'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('OSRM Service Error: ' . $e->getMessage());
            
            // Fallback to approximate calculation
            return $this->calculateApproximateRoute($pickupLat, $pickupLng, $destinationLat, $destinationLng);
        }
    }

    /**
     * Get route with waypoints
     */
    public function getRouteWithWaypoints($coordinates)
    {
        try {
            if (count($coordinates) < 2) {
                throw new \Exception('At least 2 coordinates required');
            }

            $coordinatesString = implode(';', array_map(function($coord) {
                return $coord['lng'] . ',' . $coord['lat'];
            }, $coordinates));

            $url = "{$this->baseUrl}/route/v1/driving/{$coordinatesString}";
            
            $response = Http::timeout($this->timeout)->get($url, [
                'overview' => 'full',
                'geometries' => 'polyline',
                'steps' => 'true'
            ]);

            if (!$response->successful()) {
                throw new \Exception('OSRM API request failed');
            }

            $data = $response->json();

            if (!isset($data['routes'][0])) {
                throw new \Exception('No route found');
            }

            $route = $data['routes'][0];

            return [
                'success' => true,
                'distance_km' => round($route['distance'] / 1000, 2),
                'duration_minutes' => round($route['duration'] / 60, 2),
                'polyline' => $route['geometry'],
                'legs' => $route['legs'] ?? [],
                'steps' => $this->extractSteps($route['legs'] ?? [])
            ];

        } catch (\Exception $e) {
            Log::error('OSRM Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get nearest road point
     */
    public function getNearestRoad($lat, $lng)
    {
        try {
            $url = "{$this->baseUrl}/nearest/v1/driving/{$lng},{$lat}";
            
            $response = Http::timeout($this->timeout)->get($url);

            if (!$response->successful()) {
                throw new \Exception('OSRM API request failed');
            }

            $data = $response->json();

            if (!isset($data['waypoints'][0])) {
                throw new \Exception('No nearest road found');
            }

            $waypoint = $data['waypoints'][0];

            return [
                'success' => true,
                'latitude' => $waypoint['location'][1],
                'longitude' => $waypoint['location'][0],
                'distance' => $waypoint['distance'] ?? 0
            ];

        } catch (\Exception $e) {
            Log::error('OSRM Nearest Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate approximate route as fallback
     */
    protected function calculateApproximateRoute($pickupLat, $pickupLng, $destinationLat, $destinationLng)
    {
        // Haversine formula for distance calculation
        $earthRadius = 6371; // km

        $latDelta = deg2rad($destinationLat - $pickupLat);
        $lngDelta = deg2rad($destinationLng - $pickupLng);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($pickupLat)) * cos(deg2rad($destinationLat)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        // Approximate duration (assuming average speed of 40 km/h)
        $duration = ($distance / 40) * 60; // minutes

        return [
            'success' => true,
            'distance_km' => round($distance, 2),
            'distance_m' => round($distance * 1000),
            'duration_minutes' => round($duration, 2),
            'duration_seconds' => round($duration * 60),
            'polyline' => null,
            'approximated' => true
        ];
    }

    /**
     * Extract steps from route legs
     */
    protected function extractSteps($legs)
    {
        $steps = [];
        
        foreach ($legs as $leg) {
            if (isset($leg['steps'])) {
                foreach ($leg['steps'] as $step) {
                    $steps[] = [
                        'instruction' => $step['maneuver']['instruction'] ?? '',
                        'distance' => $step['distance'] ?? 0,
                        'duration' => $step['duration'] ?? 0,
                        'geometry' => $step['geometry'] ?? null
                    ];
                }
            }
        }

        return $steps;
    }
}
