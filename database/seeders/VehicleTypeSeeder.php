<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $vehicleTypes = [
            [
                'name' => 'motorcycle',
                'display_name' => 'Motor',
                'base_fare' => 8000,
                'per_km_rate' => 2000,
                'capacity' => 1,
                'icon' => 'icons/motorcycle.png',
                'is_active' => true,
            ],
            [
                'name' => 'car',
                'display_name' => 'Mobil',
                'base_fare' => 11000,
                'per_km_rate' => 3000,
                'capacity' => 4,
                'icon' => 'icons/car.png',
                'is_active' => true,
            ],
            [
                'name' => 'van',
                'display_name' => 'Van',
                'base_fare' => 18000,
                'per_km_rate' => 4000,
                'capacity' => 8,
                'icon' => 'icons/van.png',
                'is_active' => true,
            ],
            [
                'name' => 'truck',
                'display_name' => 'Truk',
                'base_fare' => 25000,
                'per_km_rate' => 5000,
                'capacity' => 2,
                'icon' => 'icons/truck.png',
                'is_active' => false, // Temporarily disabled
            ],
        ];

        foreach ($vehicleTypes as $vehicleType) {
            VehicleType::create($vehicleType);
        }
    }
}
