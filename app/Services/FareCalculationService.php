<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FareCalculationService
{
    protected $baseFare;
    protected $perKmRate;
    protected $perMinuteRate;
    protected $minimumFare;
    protected $surchargeRates;

    public function __construct()
    {
        $this->baseFare = config('fare.base_fare', 5000);
        $this->perKmRate = config('fare.per_km_rate', 2000);
        $this->minimumFare = config('fare.minimum_fare', 8000);
        $this->surchargeRates = config('fare.surcharge_rates', [
            'night' => 0.2, // 20% surcharge
        ]);
    }

    /**
     * Calculate fare based on distance and duration
     */
    public function calculateFare($distanceKm, $durationMinutes, $vehicleType = 'car', $options = [])
    {
        try {
            // Base calculation
            $distanceFare = $distanceKm * $this->getPerKmRate($vehicleType);
            $timeFare = $durationMinutes * $this->getPerMinuteRate($vehicleType);
            $baseFare = $this->getBaseFare($vehicleType);

            $subtotal = $baseFare + $distanceFare + $timeFare;

            // Apply minimum fare
            $minimumFare = $this->getMinimumFare($vehicleType);
            if ($subtotal < $minimumFare) {
                $subtotal = $minimumFare;
            }

            // Apply surcharges
            $surcharges = $this->calculateSurcharges($subtotal, $options);
            $totalSurcharge = array_sum($surcharges);

            $total = $subtotal + $totalSurcharge;

            return [
                'success' => true,
                'data' => [
                    'base_fare' => $baseFare,
                    'distance_fare' => $distanceFare,
                    'time_fare' => $timeFare,
                    'subtotal' => $subtotal,
                    'surcharges' => $surcharges,
                    'total_surcharge' => $totalSurcharge,
                    'total' => $total,
                    'distance_km' => $distanceKm,
                    'duration_minutes' => $durationMinutes,
                    'vehicle_type' => $vehicleType,
                    'breakdown' => [
                        'per_km_rate' => $this->getPerKmRate($vehicleType),
                        'per_minute_rate' => $this->getPerMinuteRate($vehicleType),
                        'minimum_fare' => $minimumFare
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Fare Calculation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate estimated fare range
     */
    public function calculateEstimatedFare($distanceKm, $durationMinutes, $vehicleType = 'car')
    {
        $normalFare = $this->calculateFare($distanceKm, $durationMinutes, $vehicleType);
        
        if (!$normalFare['success']) {
            return $normalFare;
        }

        $baseTotal = $normalFare['data']['total'];
        
        // Calculate range with potential surcharges
        $minFare = $baseTotal;
        $maxFare = $baseTotal * (1 + $this->surchargeRates['peak_hour']); // Max with peak hour surcharge

        return [
            'success' => true,
            'data' => [
                'min_fare' => $minFare,
                'max_fare' => $maxFare,
                'base_fare' => $baseTotal,
                'currency' => 'IDR'
            ]
        ];
    }

    /**
     * Calculate surcharges based on conditions
     */
    protected function calculateSurcharges($subtotal, $options)
    {
        $surcharges = [];

        // Night surcharge (10 PM - 6 AM)
        if (isset($options['is_night']) && $options['is_night']) {
            $surcharges['night'] = $subtotal * $this->surchargeRates['night'];
        }
  
        return $surcharges;
    }

    /**
     * Get base fare by vehicle type
     */
    protected function getBaseFare($vehicleType)
    {
        $rates = [
            'motorcycle' => $this->baseFare * 0.5,
            'car' => $this->baseFare,
            'van' => $this->baseFare * 1.5,
            'truck' => $this->baseFare * 2
        ];

        return $rates[$vehicleType] ?? $this->baseFare;
    }

    /**
     * Get per km rate by vehicle type
     */
    protected function getPerKmRate($vehicleType)
    {
        $rates = [
            'motorcycle' => $this->perKmRate * 0.6,
            'car' => $this->perKmRate,
            'van' => $this->perKmRate * 1.3,
            'truck' => $this->perKmRate * 1.8
        ];

        return $rates[$vehicleType] ?? $this->perKmRate;
    }

    /**
     * Get per minute rate by vehicle type
     */
    protected function getPerMinuteRate($vehicleType)
    {
        $rates = [
            'motorcycle' => $this->perMinuteRate * 0.7,
            'car' => $this->perMinuteRate,
            'van' => $this->perMinuteRate * 1.2,
            'truck' => $this->perMinuteRate * 1.5
        ];

        return $rates[$vehicleType] ?? $this->perMinuteRate;
    }

    /**
     * Get minimum fare by vehicle type
     */
    protected function getMinimumFare($vehicleType)
    {
        $rates = [
            'motorcycle' => $this->minimumFare * 0.6,
            'car' => $this->minimumFare,
            'van' => $this->minimumFare * 1.4,
            'truck' => $this->minimumFare * 2
        ];

        return $rates[$vehicleType] ?? $this->minimumFare;
    }

    /**
     * Get current time-based conditions
     */
    public function getCurrentConditions()
    {
        $now = now();
        $hour = $now->hour;
        $dayOfWeek = $now->dayOfWeek;

        return [
            'is_night' => $hour >= 22 || $hour < 6,
            
        ];
    }

    public function calculateFinalFare($baseFare, $commissionRate = 0.1)
{
    $markup = $baseFare * $commissionRate;
    $finalFare = $baseFare + $markup;

    return [
        'base_fare' => $baseFare,
        'final_fare' => ceil($finalFare), // dibulatkan ke atas
        'commission' => ceil($markup),
    ];
}
}