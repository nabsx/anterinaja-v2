<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Base Fare Configuration
    |--------------------------------------------------------------------------
    |
    | This value is the base fare for all ride types
    |
    */

    'base_fare' => env('FARE_BASE', 5000),

    /*
    |--------------------------------------------------------------------------
    | Per Kilometer Rate
    |--------------------------------------------------------------------------
    |
    | This value is the rate charged per kilometer
    |
    */

    'per_km_rate' => env('FARE_PER_KM', 2000),

    /*
    |--------------------------------------------------------------------------
    | Per Minute Rate
    |--------------------------------------------------------------------------
    |
    | This value is the rate charged per minute
    |
    */

    'per_minute_rate' => env('FARE_PER_MINUTE', 300),

    /*
    |--------------------------------------------------------------------------
    | Minimum Fare
    |--------------------------------------------------------------------------
    |
    | This is the minimum fare that will be charged for any ride
    |
    */

    'minimum_fare' => env('FARE_MINIMUM', 8000),

    /*
    |--------------------------------------------------------------------------
    | Surcharge Rates
    |--------------------------------------------------------------------------
    |
    | These are additional charges based on conditions
    |
    */

    'surcharge_rates' => [
        'night' => 0.2,        // 20% surcharge for night time (10 PM - 6 AM)
        'peak_hour' => 0.5,    // 50% surcharge for peak hours (7-9 AM, 5-7 PM)
        'rain' => 0.3,         // 30% surcharge for rainy weather
        'holiday' => 0.25,     // 25% surcharge for holidays
    ],

    /*
    |--------------------------------------------------------------------------
    | Vehicle Type Multipliers
    |--------------------------------------------------------------------------
    |
    | These multipliers adjust the base rates for different vehicle types
    |
    */

    'vehicle_multipliers' => [
        'motorcycle' => [
            'base_fare' => 0.5,
            'per_km_rate' => 0.6,
            'per_minute_rate' => 0.7,
            'minimum_fare' => 0.6,
        ],
        'car' => [
            'base_fare' => 1.0,
            'per_km_rate' => 1.0,
            'per_minute_rate' => 1.0,
            'minimum_fare' => 1.0,
        ],
        'van' => [
            'base_fare' => 1.5,
            'per_km_rate' => 1.3,
            'per_minute_rate' => 1.2,
            'minimum_fare' => 1.4,
        ],
        'truck' => [
            'base_fare' => 2.0,
            'per_km_rate' => 1.8,
            'per_minute_rate' => 1.5,
            'minimum_fare' => 2.0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Platform Commission
    |--------------------------------------------------------------------------
    |
    | Percentage of commission taken by the platform
    |
    */

    'platform_commission' => env('PLATFORM_COMMISSION', 0.10), // 10%

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    |
    | Currency code and formatting
    |
    */

    'currency' => [
        'code' => 'IDR',
        'symbol' => 'Rp',
        'decimal_places' => 0,
    ],

];