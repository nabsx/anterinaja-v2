<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            VehicleTypeSeeder::class, // First, because drivers need vehicle types
            UserSeeder::class,
            DriverSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
