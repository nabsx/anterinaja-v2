<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\User;
use App\Models\DriverDocument;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        // Get all driver users
        $driverUsers = User::where('role', 'driver')->get();

        // Vehicle types and plates
        $vehicleTypes = ['motorcycle', 'car', 'motorcycle', 'car', 'motorcycle'];
        $vehiclePlates = [
            'B 1234 ABC',
            'B 5678 DEF',
            'B 9012 GHI',
            'B 3456 JKL',
            'B 7890 MNO',
        ];

        $licenseNumbers = [
            'SIM12345678901',
            'SIM12345678902',
            'SIM12345678903',
            'SIM12345678904',
            'SIM12345678905',
        ];

        // Jakarta coordinates (rough area)
        $jakartaCoordinates = [
            ['lat' => -6.2088, 'lng' => 106.8456], // Monas
            ['lat' => -6.1751, 'lng' => 106.8650], // Ancol
            ['lat' => -6.2297, 'lng' => 106.6890], // Bintaro
            ['lat' => -6.1745, 'lng' => 106.7840], // Kelapa Gading
            ['lat' => -6.2615, 'lng' => 106.7810], // Fatmawati
            ['lat' => -6.1944, 'lng' => 106.8222], // Menteng
            ['lat' => -6.2383, 'lng' => 106.8560], // Tebet
            ['lat' => -6.1478, 'lng' => 106.8467], // Tanjung Priok
            ['lat' => -6.2704, 'lng' => 106.8467], // Kebayoran Baru
            ['lat' => -6.1589, 'lng' => 106.8944], // Cakung
        ];

        foreach ($driverUsers as $index => $user) {
            $coordinate = $jakartaCoordinates[$index % count($jakartaCoordinates)];
            
            $driver = Driver::create([
                'user_id' => $user->id,
                'vehicle_type' => $vehicleTypes[$index % count($vehicleTypes)],
                'vehicle_plate' => 'B ' . rand(1000, 9999) . ' ' . strtoupper(Str::random(3)) . Str::random(2),
                'license_number' => 'SIM' . strtoupper(Str::random(10)),
                'is_verified' => $index < 8, // First 8 drivers are verified
                'current_latitude' => $coordinate['lat'] + (rand(-100, 100) / 10000), // Add some random offset
                'current_longitude' => $coordinate['lng'] + (rand(-100, 100) / 10000),
                'is_online' => $index < 5, // First 5 drivers are online
                'status' => $index < 3 ? 'available' : ($index < 5 ? 'busy' : 'offline'),
                'rating' => rand(350, 500) / 100, // Random rating between 3.5 - 5.0
                'total_trips' => rand(10, 500),
                'last_active_at' => now()->subMinutes(rand(0, 60)),
                'balance' => rand(10000, 100000), // Seed saldo acak antara 10.000 - 100.000
            ]);

            // Create driver documents
            $documentTypes = ['ktp', 'sim', 'stnk', 'photo'];
            foreach ($documentTypes as $docType) {
                DriverDocument::create([
                    'driver_id' => $driver->id,
                    'document_type' => $docType,
                    'document_path' => "documents/driver_{$driver->id}_{$docType}.jpg",
                ]);
            }
        }

        // Create additional random drivers
        $additionalDriverUsers = User::factory(15)->create(['role' => 'driver']);
        
        foreach ($additionalDriverUsers as $user) {
            $coordinate = $jakartaCoordinates[rand(0, count($jakartaCoordinates) - 1)];
            
            $driver = Driver::create([
                'user_id' => $user->id,
                'vehicle_type' => $vehicleTypes[rand(0, count($vehicleTypes) - 1)],
                'vehicle_plate' => 'B ' . rand(1000, 9999) . ' ' . strtoupper(Str::random(3)) . Str::random(2),
                'license_number' => 'SIM' . strtoupper(Str::random(10)),
                'is_verified' => rand(0, 1),
                'current_latitude' => $coordinate['lat'] + (rand(-200, 200) / 10000),
                'current_longitude' => $coordinate['lng'] + (rand(-200, 200) / 10000),
                'is_online' => rand(0, 1),
                'status' => ['available', 'busy', 'offline'][rand(0, 2)],
                'rating' => rand(300, 500) / 100,
                'total_trips' => rand(0, 200),
                'last_active_at' => now()->subMinutes(rand(0, 1440)), // Random within last 24 hours
                'balance' => rand(10000, 100000), // Seed saldo acak antara 10.000 - 100.000
            ]);

            // Create some documents for random drivers
            $documentTypes = ['ktp', 'sim', 'stnk', 'photo'];
            $numDocs = rand(2, 4); // Random number of documents
            
            for ($i = 0; $i < $numDocs; $i++) {
                DriverDocument::create([
                    'driver_id' => $driver->id,
                    'document_type' => $documentTypes[$i],
                    'document_path' => "documents/driver_{$driver->id}_{$documentTypes[$i]}.jpg",
                ]);
            }
        }
    }
}
