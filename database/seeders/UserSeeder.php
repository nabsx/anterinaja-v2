<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin Ojek Online',
            'email' => 'admin@ojekonline.com',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Customer Users
        $customers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'phone' => '081234567891',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti@gmail.com',
                'phone' => '081234567892',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ],
            [
                'name' => 'Ahmad Kurniawan',
                'email' => 'ahmad@gmail.com',
                'phone' => '081234567893',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@gmail.com',
                'phone' => '081234567894',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ],
            [
                'name' => 'Roni Pratama',
                'email' => 'roni@gmail.com',
                'phone' => '081234567895',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ],
        ];

        foreach ($customers as $customer) {
            User::create(array_merge($customer, [
                'is_active' => true,
                'email_verified_at' => now(),
            ]));
        }

        // Driver Users
        $drivers = [
            [
                'name' => 'Agus Setiawan',
                'email' => 'agus@driver.com',
                'phone' => '081234567896',
                'password' => Hash::make('password123'),
                'role' => 'driver',
            ],
            [
                'name' => 'Benny Wijaya',
                'email' => 'benny@driver.com',
                'phone' => '081234567897',
                'password' => Hash::make('password123'),
                'role' => 'driver',
            ],
            [
                'name' => 'Candra Permana',
                'email' => 'candra@driver.com',
                'phone' => '081234567898',
                'password' => Hash::make('password123'),
                'role' => 'driver',
            ],
            [
                'name' => 'Dedi Suryadi',
                'email' => 'dedi@driver.com',
                'phone' => '081234567899',
                'password' => Hash::make('password123'),
                'role' => 'driver',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko@driver.com',
                'phone' => '081234567800',
                'password' => Hash::make('password123'),
                'role' => 'driver',
            ],
        ];

        foreach ($drivers as $driver) {
            User::create(array_merge($driver, [
                'is_active' => true,
                'email_verified_at' => now(),
            ]));
        }

        // Create additional random users for testing
        User::factory(20)->create(['role' => 'customer']);
        User::factory(10)->create(['role' => 'driver']);
    }
}