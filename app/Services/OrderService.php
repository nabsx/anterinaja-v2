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
                $orderData['pickup_latitude'] ?? $orderData['pickup_lat'],
                $orderData['pickup_longitude'] ?? $orderData['pickup_lng'],
                $orderData['destination_latitude'] ?? $orderData['destination_lat'],
                $orderData['destination_longitude'] ?? $orderData['destination_lng']
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

            // Use the fare data directly - don't recalculate commission
            $customerFare = $fareData['data']['total']; // This already includes commission
            $driverEarning = $fareData['data']['driver_earning'] ?? ($customerFare * 0.8); // Fallback if not set
            $platformCommission = $fareData['data']['surcharges']['commission'] ?? ($customerFare * 0.2); // Fallback if not set

            // Create order
            $order = Order::create([
                'order_code' => $this->generateOrderNumber(),
                'customer_id' => $userId,
                'order_type' => $orderData['order_type'] ?? 'ride',
                'pickup_address' => $orderData['pickup_address'],
                'pickup_latitude' => $orderData['pickup_latitude'],
                'pickup_longitude' => $orderData['pickup_longitude'],
                'destination_address' => $orderData['destination_address'],
                'destination_latitude' => $orderData['destination_latitude'],
                'destination_longitude' => $orderData['destination_longitude'],
                'vehicle_type' => $orderData['vehicle_type'] ?? 'car',
                'distance_km' => $routeData['distance_km'],
                'duration_minutes' => $routeData['duration_minutes'],
                'fare_amount' => $customerFare,
                'driver_earning' => $driverEarning,
                'platform_commission' => $platformCommission,
                'fare_breakdown' => $fareData['data'] ?? [],
                'status' => 'pending',
                'notes' => $orderData['notes'] ?? null,
                'scheduled_at' => $orderData['scheduled_at'] ?? null,
            ]);

            DB::commit();

            // Send notifications to available drivers
            $this->notifyAvailableDrivers($order);

            return [
                'success' => true,
                'data' => $order->load('customer'),
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
                    
                    // Use the stored driver earning instead of recalculating
                    if ($order->driver_id) {
                        $driver = Driver::find($order->driver_id);
                        if ($driver) {
                            // Add the pre-calculated driver earning to driver balance
                            $driver->balance += $order->driver_earning;
                            $driver->save();
                        }
                    }
                    break;

                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    $updateData['cancellation_reason'] = $additionalData['reason'] ?? null;
                    $updateData['cancelled_by'] = $additionalData['cancelled_by'] ?? 'system';
                    break;
            }

            $order->update($updateData);

            // Send notifications
            $this->sendStatusNotification($order, $status);

            return [
                'success' => true,
                'data' => $order->fresh()->load(['customer', 'driver']),
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
                    cos( radians( drivers.current_latitude ) ) * 
                    cos( radians( drivers.current_longitude ) - radians(?) ) + 
                    sin( radians(?) ) * 
                    sin( radians( drivers.current_latitude ) ) ) ) AS distance
                ', [$order->pickup_latitude, $order->pickup_longitude, $order->pickup_latitude])
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
     * Cancel order - IMPROVED VERSION
     */
    public function cancelOrder($orderId, $reason, $cancelledBy = 'user')
    {
        try {
            DB::beginTransaction();

            // Find the order with proper error handling
            $order = Order::find($orderId);
            
            if (!$order) {
                throw new \Exception('Order not found');
            }

            // Check if order can be cancelled
            if (!$this->canOrderBeCancelled($order)) {
                throw new \Exception('Order cannot be cancelled. Current status: ' . $order->status);
            }

            // Log the cancellation attempt
            Log::info('Attempting to cancel order', [
                'order_id' => $orderId,
                'current_status' => $order->status,
                'cancelled_by' => $cancelledBy,
                'reason' => $reason
            ]);

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $cancelledBy,
                'cancellation_reason' => $reason,
            ]);

            // If driver was assigned, make them available again
            if ($order->driver_id) {
                $driver = Driver::find($order->driver_id);
                if ($driver) {
                    $driver->update([
                        'is_available' => true,
                    ]);
                    
                    Log::info('Driver made available after order cancellation', [
                        'driver_id' => $driver->id,
                        'order_id' => $orderId
                    ]);
                }
            }

            DB::commit();

            // Send cancellation notifications
            $this->sendCancellationNotification($order);

            Log::info('Order cancelled successfully', [
                'order_id' => $orderId,
                'cancelled_by' => $cancelledBy
            ]);

            return [
                'success' => true,
                'data' => $order->fresh(),
                'message' => 'Order cancelled successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order Cancellation Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'cancelled_by' => $cancelledBy,
                'reason' => $reason,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if order can be cancelled
     */
    private function canOrderBeCancelled($order)
    {
        $cancellableStatuses = ['pending', 'accepted', 'driver_arrived'];
        
        return in_array($order->status, $cancellableStatuses);
    }

    /**
     * Get order details
     */
    public function getOrderDetails($orderId)
    {
        try {
            $order = Order::with(['customer', 'driver', 'ratings'])->findOrFail($orderId);

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
                ->where('customer_id', $userId)
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
            $query = Order::with(['customer'])
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
        try {
            $driversResult = $this->findAvailableDrivers($order);
            
            if ($driversResult['success'] && $driversResult['count'] > 0) {
                foreach ($driversResult['data'] as $driver) {
                    $this->notificationService->sendOrderNotification($driver, $order, 'new_order');
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify drivers: ' . $e->getMessage());
            // Don't throw exception as order creation was successful
        }
    }

    /**
     * Send status notification
     */
    protected function sendStatusNotification($order, $status)
    {
        try {
            // Notify customer
            $this->notificationService->sendOrderStatusNotification($order->customer, $order, $status);

            // Notify driver if applicable
            if ($order->driver) {
                $this->notificationService->sendOrderStatusNotification($order->driver->user, $order, $status);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send status notification: ' . $e->getMessage());
            // Don't throw exception as the main operation was successful
        }
    }

    /**
     * Send cancellation notification - IMPROVED VERSION
     */
    protected function sendCancellationNotification($order)
    {
        try {
            // Notify customer
            if ($order->customer) {
                $this->notificationService->sendCancellationNotification($order->customer, $order);
            }
            
            // Notify driver if assigned
            if ($order->driver_id && $order->driver && $order->driver->user) {
                $this->notificationService->sendCancellationNotification($order->driver->user, $order);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation notification: ' . $e->getMessage(), [
                'order_id' => $order->id
            ]);
            // Don't throw exception here as the main cancellation was successful
        }
    }

    /**
     * Rate order
     */
    public function rateOrder($order, $rating, $comment, $ratedBy)
    {
        try {
            DB::beginTransaction();

            // Create rating record
            $order->ratings()->create([
                'rating' => $rating,
                'comment' => $comment,
                'rated_by' => $ratedBy,
                'created_at' => now()
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Rating submitted successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rating submission error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Failed to submit rating'
            ];
        }
    }
}
