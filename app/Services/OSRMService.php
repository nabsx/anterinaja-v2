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
        $this->baseUrl = config('services.osrm.url', 'http://router.project-osrm.org');
        $this->timeout = config('services.osrm.timeout', 30);
    }

    /**
     * Get route between two points
     */
    public function getRoute($startLat, $startLng, $endLat, $endLng, $alternatives = false)
    {
        try {
            $coordinates = "{$startLng},{$startLat};{$endLng},{$endLat}";
            
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/route/v1/driving/{$coordinates}", [
                'alternatives' => $alternatives ? 'true' : 'false',
                'geometries' => 'geojson',
                'overview' => 'full',
                'steps' => 'true'
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get route from OSRM'
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
     * Get distance and duration between two points
     */
    public function getDistanceAndDuration($startLat, $startLng, $endLat, $endLng)
    {
        $route = $this->getRoute($startLat, $startLng, $endLat, $endLng);

        if ($route['success'] && isset($route['data']['routes'][0])) {
            $routeData = $route['data']['routes'][0];
            return [
                'success' => true,
                'distance' => $routeData['distance'], // in meters
                'duration' => $routeData['duration'], // in seconds
                'distance_km' => round($routeData['distance'] / 1000, 2),
                'duration_minutes' => round($routeData['duration'] / 60, 2)
            ];
        }

        return [
            'success' => false,
            'error' => 'Unable to calculate distance and duration'
        ];
    }

    /**
     * Get multiple routes (matrix)
     */
    public function getMatrix($coordinates)
    {
        try {
            $coordString = collect($coordinates)->map(function($coord) {
                return $coord['lng'] . ',' . $coord['lat'];
            })->join(';');

            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/table/v1/driving/{$coordString}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get matrix from OSRM'
            ];

        } catch (\Exception $e) {
            Log::error('OSRM Matrix Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get nearest road point
     */
    public function getNearestPoint($lat, $lng)
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/nearest/v1/driving/{$lng},{$lat}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get nearest point from OSRM'
            ];

        } catch (\Exception $e) {
            Log::error('OSRM Nearest Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}