<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use App\Models\Delivery;
use App\Models\OrderTracking;
use App\Models\Rating;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $drivers = Driver::where('is_verified', true)->get();

        // Jakarta popular locations
        $locations = [
            ['address' => 'Monas, Jakarta Pusat', 'lat' => -6.2088, 'lng' => 106.8456],
            ['address' => 'Grand Indonesia, Jakarta Pusat', 'lat' => -6.1944, 'lng' => 106.8222],
            ['address' => 'Ancol, Jakarta Utara', 'lat' => -6.1251, 'lng' => 106.8650],
            ['address' => 'Blok M, Jakarta Selatan', 'lat' => -6.2297, 'lng' => 106.7991],
            ['address' => 'Kelapa Gading, Jakarta Utara', 'lat' => -6.1745, 'lng' => 106.7840],
            ['address' => 'Pondok Indah, Jakarta Selatan', 'lat' => -6.2615, 'lng' => 106.7810],
            ['address' => 'Senayan, Jakarta Pusat', 'lat' => -6.2383, 'lng' => 106.8017],
            ['address' => 'Kemang, Jakarta Selatan', 'lat' => -6.2704, 'lng' => 106.8156],
            ['address' => 'Menteng, Jakarta Pusat', 'lat' => -6.1944, 'lng' => 106.8222],
            ['address' => 'Thamrin, Jakarta Pusat', 'lat' => -6.1928, 'lng' => 106.8186],
            ['address' => 'Sudirman, Jakarta Pusat', 'lat' => -6.2088, 'lng' => 106.8228],
            ['address' => 'Kuningan, Jakarta Selatan', 'lat' => -6.2297, 'lng' => 106.8306],
        ];

        $orderStatuses = ['pending', 'accepted', 'driver_arrived', 'picked_up', 'in_progress', 'completed', 'cancelled'];
        $orderTypes = ['ride', 'delivery'];

        // Create orders
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $driver = $drivers->random();
            
            $pickupLocation = $locations[rand(0, count($locations) - 1)];
            $destinationLocation = $locations[rand(0, count($locations) - 1)];
            
            // Ensure pickup and destination are different
            while ($pickupLocation === $destinationLocation) {
                $destinationLocation = $locations[rand(0, count($locations) - 1)];
            }

            $orderType = $orderTypes[rand(0, 1)];
            $status = $orderStatuses[rand(0, count($orderStatuses) - 1)];
            
            // Calculate distance (rough calculation)
            $distance = $this->calculateDistance(
                $pickupLocation['lat'], $pickupLocation['lng'],
                $destinationLocation['lat'], $destinationLocation['lng']
            );

            $fareAmount = 5000 + ($distance * 2000); // Base fare + distance fare
            $estimatedDuration = $distance * 3; // Rough estimate: 3 minutes per km

            $order = Order::create([
                'order_code' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'driver_id' => in_array($status, ['accepted', 'driver_arrived', 'picked_up', 'in_progress', 'completed']) ? $driver->id : null,
                'order_type' => $orderType,
                'pickup_address' => $pickupLocation['address'],
                'pickup_latitude' => $pickupLocation['lat'],
                'pickup_longitude' => $pickupLocation['lng'],
                'destination_address' => $destinationLocation['address'],
                'destination_latitude' => $destinationLocation['lat'],
                'destination_longitude' => $destinationLocation['lng'],
                'distance_km' => $distance,
                'estimated_duration' => $estimatedDuration,
                'fare_amount' => $fareAmount,
                'notes' => $i % 3 == 0 ? 'Tolong jangan terlambat' : null,
                'status' => $status,
                'accepted_at' => in_array($status, ['accepted', 'driver_arrived', 'picked_up', 'in_progress', 'completed']) ? now()->subMinutes(rand(5, 60)) : null,
                'picked_up_at' => in_array($status, ['picked_up', 'in_progress', 'completed']) ? now()->subMinutes(rand(5, 30)) : null,
                'completed_at' => $status === 'completed' ? now()->subMinutes(rand(1, 15)) : null,
                'cancelled_at' => $status === 'cancelled' ? now()->subMinutes(rand(1, 30)) : null,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);

            // Create delivery details if order type is delivery
            if ($orderType === 'delivery') {
                $deliveryItems = [
                    'Paket makanan',
                    'Dokumen penting',
                    'Pakaian',
                    'Elektronik',
                    'Obat-obatan',
                    'Buku',
                    'Kue ulang tahun',
                    'Bunga',
                ];

                Delivery::create([
                    'order_id' => $order->id,
                    'item_description' => $deliveryItems[rand(0, count($deliveryItems) - 1)],
                    'item_weight' => rand(1, 50) / 10, // Random weight 0.1 - 5.0 kg
                    'recipient_name' => fake()->name(),
                    'recipient_phone' => '081' . rand(100000000, 999999999),
                    'special_instructions' => $i % 4 == 0 ? 'Barang mudah pecah, harap hati-hati' : null,
                ]);
            }

            // Create order tracking history
            $this->createOrderTrackings($order);

            // Create ratings for completed orders
            if ($status === 'completed' && $order->driver_id) {
                // Customer rating driver
                Rating::create([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'driver_id' => $order->driver_id,
                    'rated_by' => 'customer',
                    'rating' => rand(3, 5),
                    'review' => $i % 3 == 0 ? 'Driver sangat ramah dan tepat waktu' : null,
                ]);

                // Driver rating customer
                Rating::create([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'driver_id' => $order->driver_id,
                    'rated_by' => 'driver',
                    'rating' => rand(4, 5),
                    'review' => $i % 4 == 0 ? 'Customer sangat baik dan mudah dihubungi' : null,
                ]);
            }
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    private function createOrderTrackings(Order $order)
    {
        $trackings = [];

        // Always create 'pending' tracking
        $trackings[] = [
            'order_id' => $order->id,
            'status' => 'pending',
            'notes' => 'Order created, waiting for driver',
            'created_at' => $order->created_at,
        ];

        // Create trackings based on order status
        if (in_array($order->status, ['accepted', 'driver_arrived', 'picked_up', 'in_progress', 'completed'])) {
            $trackings[] = [
                'order_id' => $order->id,
                'status' => 'accepted',
                'notes' => 'Order accepted by driver',
                'created_at' => $order->accepted_at,
            ];
        }

        if (in_array($order->status, ['driver_arrived', 'picked_up', 'in_progress', 'completed'])) {
            $trackings[] = [
                'order_id' => $order->id,
                'status' => 'driver_arrived',
                'notes' => 'Driver arrived at pickup location',
                'created_at' => $order->accepted_at->addMinutes(rand(5, 15)),
            ];
        }

        if (in_array($order->status, ['picked_up', 'in_progress', 'completed'])) {
            $trackings[] = [
                'order_id' => $order->id,
                'status' => 'picked_up',
                'notes' => $order->order_type === 'ride' ? 'Customer picked up' : 'Item picked up',
                'created_at' => $order->picked_up_at,
            ];
        }

        if (in_array($order->status, ['in_progress', 'completed'])) {
            $trackings[] = [
                'order_id' => $order->id,
                'status' => 'in_progress',
                'notes' => 'On the way to destination',
                'created_at' => $order->picked_up_at->addMinutes(rand(2, 5)),
            ];
        }

        if ($order->status === 'completed') {
            $trackings[] = [
                'order_id' => $order->id,
                'status' => 'completed',
                'notes' => 'Order completed successfully',
                'created_at' => $order->completed_at,
            ];
        }

        if ($order->status === 'cancelled') {
            $trackings[] = [
                'order_id' => $order->id,
                'status' => 'cancelled',
                'notes' => 'Order cancelled',
                'created_at' => $order->cancelled_at,
            ];
        }

        // Insert trackings
        foreach ($trackings as $tracking) {
            OrderTracking::create($tracking);
        }
    }
}
