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
        ]);
    }

    /**
     * Calculate fare based on distance and duration
     */
    public function calculateFare($distanceKm, $durationMinutes, $serviceType, $conditions)
{
    $baseFare = 0;
    $perKmRate = 0;

    if ($serviceType === 'motorcycle') {
        if ($distanceKm <= 4) {
            $baseFare = 8000;
            $perKmRate = 0;
        } else {
            $baseFare = 8000;
            $perKmRate = 2000;
        }
    } elseif ($serviceType === 'car') {
        if ($distanceKm <= 4) {
            $baseFare = 10000;
            $perKmRate = 0;
        } else {
            $baseFare = 10000;
            $perKmRate = 3500;
        }
    } else {
        return [
            'success' => false,
            'error' => 'Jenis layanan tidak dikenali.'
        ];
    }

    $distanceFare = $perKmRate * max(0, $distanceKm - 4);
    $subtotal = $baseFare + $distanceFare;
    $commission = $subtotal * 0.10;
    $totalFare = $subtotal + $commission;

    return [
        'success' => true,
        'data' => [
            'base_fare' => $baseFare,
            'distance_fare' => $distanceFare,
            'time_fare' => 0,
            'subtotal' => $subtotal,
            'surcharges' => [
                'commission' => $commission
            ],
            'total_surcharge' => $commission,
            'total' => $totalFare,
            'breakdown' => [
                'base_fare' => $baseFare,
                'distance_fare' => $distanceFare,
                'commission' => $commission,
            ]
        ]
    ];
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
     
   * protected function getPerMinuteRate($vehicleType)
    *{
       * $multipliers = config('fare.vehicle_multipliers', []);
        
       * if (isset($multipliers[$vehicleType]['per_minute_rate'])) {
        *    return $this->perMinuteRate * $multipliers[$vehicleType]['per_minute_rate'];
      *  }

       * // Fallback to hardcoded rates
       * $rates = [
        *    'motorcycle' => $this->perMinuteRate * 0.7,
         *   'car' => $this->perMinuteRate,
          *  'van' => $this->perMinuteRate * 1.2,
           * 'truck' => $this->perMinuteRate * 1.5
     *   ];

      *  return $rates[$vehicleType] ?? $this->perMinuteRate;
  *  }
*/
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