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
        $this->perMinuteRate = config('fare.per_minute_rate', 300);
        $this->baseFare = config('fare.base_fare', 5000);
        $this->perKmRate = config('fare.per_km_rate', 2000);
        $this->minimumFare = config('fare.minimum_fare', 8000);
        $this->surchargeRates = config('fare.surcharge_rates', [
            'night' => 0.2,        // 20% surcharge
            'peak_hour' => 0.5,    // 50% surcharge for peak hours
            'rain' => 0.3,         // 30% surcharge for rainy weather
            'holiday' => 0.25,     // 25% surcharge for holidays
        ]);
    }

    /**
     * Calculate fare based on distance and duration
     */
    public function calculateFare($distanceKm, $durationMinutes, $vehicleType = 'car', $options = [])
    {
        try {
            // Validate inputs
            if ($distanceKm < 0 || $durationMinutes < 0) {
                throw new \Exception('Distance and duration must be positive values');
            }

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
            Log::error('Fare Calculation Error: ' . $e->getMessage(), [
                'distance_km' => $distanceKm,
                'duration_minutes' => $durationMinutes,
                'vehicle_type' => $vehicleType,
                'options' => $options
            ]);
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
        $maxFare = $baseTotal * (1 + ($this->surchargeRates['peak_hour'] ?? 0.5)); // Max with peak hour surcharge

        return [
            'success' => true,
            'data' => [
                'min_fare' => $minFare,
                'max_fare' => $maxFare,
                'base_fare' => $baseTotal,
                'currency' => config('fare.currency.code', 'IDR')
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

        // Peak hour surcharge (7-9 AM, 5-7 PM)
        if (isset($options['is_peak_hour']) && $options['is_peak_hour']) {
            $surcharges['peak_hour'] = $subtotal * $this->surchargeRates['peak_hour'];
        }

        // Rain surcharge
        if (isset($options['is_rain']) && $options['is_rain']) {
            $surcharges['rain'] = $subtotal * $this->surchargeRates['rain'];
        }

        // Holiday surcharge
        if (isset($options['is_holiday']) && $options['is_holiday']) {
            $surcharges['holiday'] = $subtotal * $this->surchargeRates['holiday'];
        }
  
        return $surcharges;
    }

    /**
     * Get base fare by vehicle type
     */
    protected function getBaseFare($vehicleType)
    {
        $multipliers = config('fare.vehicle_multipliers', []);
        
        if (isset($multipliers[$vehicleType]['base_fare'])) {
            return $this->baseFare * $multipliers[$vehicleType]['base_fare'];
        }

        // Fallback to hardcoded rates
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
        $multipliers = config('fare.vehicle_multipliers', []);
        
        if (isset($multipliers[$vehicleType]['per_km_rate'])) {
            return $this->perKmRate * $multipliers[$vehicleType]['per_km_rate'];
        }

        // Fallback to hardcoded rates
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
        $multipliers = config('fare.vehicle_multipliers', []);
        
        if (isset($multipliers[$vehicleType]['per_minute_rate'])) {
            return $this->perMinuteRate * $multipliers[$vehicleType]['per_minute_rate'];
        }

        // Fallback to hardcoded rates
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
        $multipliers = config('fare.vehicle_multipliers', []);
        
        if (isset($multipliers[$vehicleType]['minimum_fare'])) {
            return $this->minimumFare * $multipliers[$vehicleType]['minimum_fare'];
        }

        // Fallback to hardcoded rates
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
            'is_peak_hour' => ($hour >= 7 && $hour <= 9) || ($hour >= 17 && $hour <= 19),
            'is_holiday' => $dayOfWeek == 0 || $dayOfWeek == 6, // Weekend as holiday
            'is_rain' => false, // This would need weather API integration
        ];
    }

    /**
     * Get fare breakdown for display
     */
    public function getFareBreakdown($distanceKm, $durationMinutes, $vehicleType = 'car', $options = [])
    {
        $fareResult = $this->calculateFare($distanceKm, $durationMinutes, $vehicleType, $options);
        
        if (!$fareResult['success']) {
            return $fareResult;
        }

        $data = $fareResult['data'];
        
        return [
            'success' => true,
            'breakdown' => [
                'Base Fare' => 'Rp ' . number_format($data['base_fare'], 0, ',', '.'),
                'Distance (' . $data['distance_km'] . ' km)' => 'Rp ' . number_format($data['distance_fare'], 0, ',', '.'),
                'Time (' . ceil($data['duration_minutes']) . ' minutes)' => 'Rp ' . number_format($data['time_fare'], 0, ',', '.'),
                'Subtotal' => 'Rp ' . number_format($data['subtotal'], 0, ',', '.'),
                'Surcharges' => $this->formatSurcharges($data['surcharges']),
                'Total' => 'Rp ' . number_format($data['total'], 0, ',', '.')
            ]
        ];
    }

    /**
     * Format surcharges for display
     */
    protected function formatSurcharges($surcharges)
    {
        if (empty($surcharges)) {
            return 'None';
        }

        $formatted = [];
        foreach ($surcharges as $type => $amount) {
            $formatted[] = ucfirst(str_replace('_', ' ', $type)) . ': Rp ' . number_format($amount, 0, ',', '.');
        }

        return implode(', ', $formatted);
    }
}