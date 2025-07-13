<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    protected $osrmService;
    protected $fareCalculationService;
    protected $notificationService;

    public function __construct(
        OSRMService $osrmService,
        FareCalculationService $fareCalculationService,
        NotificationService $notificationService
    ) {
        $this->osrmService = $osrmService;
        $this->fareCalculationService = $fareCalculationService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new order
     */
    public function createOrder($userId, $orderData)
    {
        try {
            DB::beginTransaction();

            // Calculate route and fare
            $routeData = $this->osrmService->getDistanceAndDuration(
                $orderData['pickup_lat'],
                $orderData['pickup_lng'],
                $orderData['destination_lat'],
                $orderData['destination_lng']
            );

            if (!$routeData['success']) {
                throw new \Exception('Unable to calculate route: ' . ($routeData['error'] ?? 'Route calculation failed'));
            }

            // Calculate fare
            $fareData = $this->fareCalculationService->calculateFare(
                $routeData['distance_km'],
                $routeData['duration_minutes'],
                $orderData['vehicle_type'] ?? 'car',
                $this->fareCalculationService->getCurrentConditions()
            );

            if (!$fareData['success']) {
                throw new \Exception('Unable to calculate fare: ' . ($fareData['error'] ?? 'Fare calculation failed'));
            }

            // Komisi platform
            $driverFare = $fareData['data']['total'];
            $platformCommission = round($driverFare * config('fare.platform_commission', 0.10));
            $customerFare = $driverFare + $platformCommission;

            // Create order
            $order = Order::create([
                'order_code' => $this->generateOrderNumber(),
                'customer_id' => $userId,
                'pickup_address' => $orderData['pickup_address'],
                'pickup_latitude' => $orderData['pickup_lat'],
                'pickup_longitude' => $orderData['pickup_lng'],
                'destination_address' => $orderData['destination_address'],
                'destination_latitude' => $orderData['destination_lat'],
                'destination_longitude' => $orderData['destination_lng'],
                'vehicle_type' => $orderData['vehicle_type'] ?? 'car',
                'distance_km' => $routeData['distance_km'],
                'duration_minutes' => $routeData['duration_minutes'],
                'fare_amount' => $customerFare,
                'fare_breakdown' => json_encode([
                    'base_fare' => $fareData['data']['base_fare'],
                    'distance_fare' => $fareData['data']['distance_fare'],
                    'time_fare' => $fareData['data']['time_fare'],
                    'subtotal' => $fareData['data']['subtotal'],
                    'surcharges' => $fareData['data']['surcharges'],
                    'total_surcharge' => $fareData['data']['total_surcharge'],
                    'total_driver' => $driverFare,
                    'commission' => $platformCommission,
                    'total_customer' => $customerFare,
                    'breakdown' => $fareData['data']['breakdown']
                ]),
                'status' => 'pending',
                'notes' => $orderData['notes'] ?? null,
                'scheduled_at' => $orderData['scheduled_at'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Send notifications to available drivers
            $this->notifyAvailableDrivers($order);

            return [
                'success' => true,
                'data' => $order->load('user'),
                'message' => 'Order created successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Creation Error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'order_data' => $orderData,
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status, $driverId = null, $additionalData = [])
    {
        try {
            $order = Order::findOrFail($orderId);
            
            $updateData = [
                'status' => $status,
                'updated_at' => now()
            ];

            // Handle status-specific updates
            switch ($status) {
                case 'accepted':
                    if (!$driverId) {
                        throw new \Exception('Driver ID is required for accepted status');
                    }
                    $updateData['driver_id'] = $driverId;
                    $updateData['accepted_at'] = now();
                    break;

                case 'driver_arrived':
                    $updateData['driver_arrived_at'] = now();
                    break;

                case 'in_progress':
                    $updateData['started_at'] = now();
                    break;

                case 'completed':
                    $updateData['completed_at'] = now();
                    if (isset($additionalData['actual_fare'])) {
                        $updateData['actual_fare'] = $additionalData['actual_fare'];
                    }
                    
                    // Potong komisi platform
                    $fareBreakdown = json_decode($order->fare_breakdown, true);
                    $commission = $fareBreakdown['commission'] ?? 0;

                    if ($driver) {
                        // Add fare to driver balance minus commission
                        $driverEarning = $order->estimated_fare - $commission;
                        $driver->balance += $driverEarning;
                        $driver->save();
                    }
                    break;

                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    $updateData['cancellation_reason'] = $additionalData['reason'] ?? null;
                    break;
            }

            $order->update($updateData);

            // Send notifications
            $this->sendStatusNotification($order, $status);

            return [
                'success' => true,
                'data' => $order->fresh()->load(['user', 'driver']),
                'message' => "Order status updated to {$status}"
            ];

        } catch (\Exception $e) {
            Log::error('Order Status Update Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'status' => $status,
                'driver_id' => $driverId,
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Find available drivers for an order
     */
    public function findAvailableDrivers($order, $radiusKm = 5)
    {
        try {
            $drivers = Driver::select('drivers.*')
                ->selectRaw('
                    ( 6371 * acos( cos( radians(?) ) * 
                    cos( radians( drivers.current_lat ) ) * 
                    cos( radians( drivers.current_lng ) - radians(?) ) + 
                    sin( radians(?) ) * 
                    sin( radians( drivers.current_lat ) ) ) ) AS distance
                ', [$order->pickup_lat, $order->pickup_longitude, $order->pickup_latitude])
                ->where('is_active', true)
                ->where('is_available', true)
                ->where('vehicle_type', $order->vehicle_type)
                ->having('distance', '<=', $radiusKm)
                ->orderBy('distance', 'asc')
                ->limit(10)
                ->get();

            return [
                'success' => true,
                'data' => $drivers,
                'count' => $drivers->count()
            ];

        } catch (\Exception $e) {
            Log::error('Find Available Drivers Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId, $reason, $cancelledBy = 'user')
    {
        try {
            $order = Order::findOrFail($orderId);

            if (in_array($order->status, ['completed', 'cancelled'])) {
                throw new \Exception('Cannot cancel order with status: ' . $order->status);
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $cancelledBy,
                'cancellation_reason' => $reason
            ]);

            // Send cancellation notifications
            $this->sendCancellationNotification($order);

            return [
                'success' => true,
                'data' => $order->fresh(),
                'message' => 'Order cancelled successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Order Cancellation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails($orderId)
    {
        try {
            $order = Order::with(['user', 'driver', 'rating'])->findOrFail($orderId);

            return [
                'success' => true,
                'data' => $order
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Order not found'
            ];
        }
    }

    /**
     * Get user's order history
     */
    public function getUserOrderHistory($userId, $limit = 20, $status = null)
    {
        try {
            $query = Order::with(['driver'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc');

            if ($status) {
                $query->where('status', $status);
            }

            $orders = $query->limit($limit)->get();

            return [
                'success' => true,
                'data' => $orders,
                'count' => $orders->count()
            ];

        } catch (\Exception $e) {
            Log::error('Get User Order History Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get driver's order history
     */
    public function getDriverOrderHistory($driverId, $limit = 20, $status = null)
    {
        try {
            $query = Order::with(['user'])
                ->where('driver_id', $driverId)
                ->orderBy('created_at', 'desc');

            if ($status) {
                $query->where('status', $status);
            }

            $orders = $query->limit($limit)->get();

            return [
                'success' => true,
                'data' => $orders,
                'count' => $orders->count()
            ];

        } catch (\Exception $e) {
            Log::error('Get Driver Order History Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_code', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Notify available drivers about new order
     */
    protected function notifyAvailableDrivers($order)
    {
        $driversResult = $this->findAvailableDrivers($order);
        
        if ($driversResult['success'] && $driversResult['count'] > 0) {
            foreach ($driversResult['data'] as $driver) {
                $this->notificationService->sendOrderNotification($driver, $order, 'new_order');
            }
        }
    }

    /**
     * Send status notification
     */
    protected function sendStatusNotification($order, $status)
    {
        // Notify user
        $this->notificationService->sendOrderStatusNotification($order->user, $order, $status);

        // Notify driver if applicable
        if ($order->driver) {
            $this->notificationService->sendOrderStatusNotification($order->driver, $order, $status);
        }
    }

    /**
     * Send cancellation notification
     */
    protected function sendCancellationNotification($order)
    {
        $this->notificationService->sendCancellationNotification($order->user, $order);
        
        if ($order->driver) {
            $this->notificationService->sendCancellationNotification($order->driver, $order);
        }
    }
}