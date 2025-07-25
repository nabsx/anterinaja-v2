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
            DB::beginTransaction();
            
            // Get the order - handle both Order object and ID
            if (is_object($orderId)) {
                $order = $orderId;
            } else {
                $order = Order::findOrFail($orderId);
            }
            
            // Verify driver ownership if driverId is provided
            if ($driverId && $order->driver_id !== $driverId) {
                throw new \Exception('Unauthorized: Driver does not own this order');
            }
        
            $updateData = [
                'status' => $status,
                'updated_at' => now()
            ];

            // Handle status-specific updates
            switch ($status) {
                case 'driver_arrived':
                    $updateData['pickup_arrived_at'] = now();
                    break;

                case 'picked_up':
                    $updateData['started_at'] = now();
                    break;

                case 'in_progress':
                    $updateData['started_at'] = $updateData['started_at'] ?? now();
                    break;

                case 'completed':
                    $updateData['completed_at'] = now();
                    if (isset($additionalData['actual_fare'])) {
                        $updateData['actual_fare'] = $additionalData['actual_fare'];
                    }
                    
                    // FIXED LOGIC: Deduct platform commission from driver balance AND increment total_trips
                    if ($order->driver_id) {
                        $driver = Driver::find($order->driver_id);
                        if ($driver) {
                            // Check if driver has sufficient balance for commission
                            if ($driver->balance < $order->platform_commission) {
                                throw new \Exception('Insufficient balance. Driver balance: Rp ' . number_format($driver->balance, 0, ',', '.') . 
                                                   ', Required commission: Rp ' . number_format($order->platform_commission, 0, ',', '.'));
                            }
                            
                            // Deduct platform commission from driver balance
                            $driver->balance -= $order->platform_commission;
                            
                            // INCREMENT TOTAL TRIPS - THIS WAS MISSING!
                            $driver->total_trips += 1;
                            
                            // Make driver available again
                            $driver->status = 'available';
                            $driver->save();
                            
                            Log::info('Order completed - driver updated', [
                                'driver_id' => $driver->id,
                                'order_id' => $order->id,
                                'commission_deducted' => $order->platform_commission,
                                'remaining_balance' => $driver->balance,
                                'total_trips' => $driver->total_trips
                            ]);
                        }
                    }
                    break;

                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    $updateData['cancellation_reason'] = $additionalData['reason'] ?? null;
                    $updateData['cancelled_by'] = $additionalData['cancelled_by'] ?? 'system';
                    
                    // Make driver available again if assigned
                    if ($order->driver_id) {
                        $driver = Driver::find($order->driver_id);
                        if ($driver) {
                            $driver->update(['status' => 'available']);
                        }
                    }
                    break;
            }

            $order->update($updateData);

            DB::commit();

            // Send notifications
            $this->sendStatusNotification($order, $status);

            return [
                'success' => true,
                'data' => $order->fresh()->load(['customer', 'driver']),
                'message' => "Order status updated to {$status}"
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Status Update Error: ' . $e->getMessage(), [
                'order_id' => is_object($orderId) ? $orderId->id : $orderId,
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
                ->where('is_online', true)
                ->where('is_verified', true)
                ->where('status', 'available')
                ->where('vehicle_type', $order->vehicle_type)
                ->where('balance', '>=', $order->platform_commission) // Check if driver has sufficient balance
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
                        'status' => 'available',
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
    public function rateOrder(Order $order, $rating, $review = null, $ratedBy = 'customer')
    {
        try {
            DB::beginTransaction();

            // Validate order is completed
            if ($order->status !== 'completed') {
                throw new \Exception('Can only rate completed orders');
            }

            // Check if already rated
            $existingRating = Rating::where('order_id', $order->id)
                ->where('rated_by', $ratedBy)
                ->first();

            if ($existingRating) {
                throw new \Exception('Order has already been rated');
            }

            // Create rating
            $ratingData = [
                'order_id' => $order->id,
                'rated_by' => $ratedBy,
                'rating' => $rating,
                'review' => $review,
            ];

            if ($ratedBy === 'customer') {
                $ratingData['customer_id'] = $order->customer_id;
                $ratingData['driver_id'] = $order->driver_id;
            } else {
                $ratingData['driver_id'] = $order->driver_id;
                $ratingData['customer_id'] = $order->customer_id;
            }

            $ratingRecord = Rating::create($ratingData);

            // Update driver's average rating if rated by customer
            if ($ratedBy === 'customer' && $order->driver) {
                $avgRating = Rating::where('driver_id', $order->driver_id)
                    ->where('rated_by', 'customer')
                    ->avg('rating');
                
                $order->driver->update(['rating' => round($avgRating, 2)]);
            }

            DB::commit();

            Log::info('Order rated successfully', [
                'order_id' => $order->id,
                'rated_by' => $ratedBy,
                'rating' => $rating
            ]);

            return [
                'success' => true,
                'data' => $ratingRecord
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order rating failed', [
                'order_id' => $order->id,
                'rated_by' => $ratedBy,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function acceptOrder(Order $order, Driver $driver)
    {
        try {
            DB::beginTransaction();

            // Log driver status for debugging
            Log::info('Attempting to accept order', [
                'order_id' => $order->id,
                'order_status' => $order->status,
                'driver_id' => $driver->id,
                'driver_is_verified' => $driver->is_verified,
                'driver_is_online' => $driver->is_online,
                'driver_status' => $driver->status,
                'driver_balance' => $driver->balance,
                'required_commission' => $order->platform_commission
            ]);

            // Check if order is still available
            if ($order->status !== 'pending') {
                throw new \Exception('Order is no longer available. Current status: ' . $order->status);
            }

            // Check if driver is verified
            if (!$driver->is_verified) {
                throw new \Exception('Driver is not verified. Please complete verification process.');
            }

            // Check if driver is online and available (status should be 'available')
            if (!$driver->is_online) {
                throw new \Exception('Driver is not online. Please go online first.');
            }

            if ($driver->status !== 'available') {
                throw new \Exception('Driver is not available. Current status: ' . $driver->status);
            }

            // FIXED: Check if driver has sufficient balance for commission
            if ($driver->balance < $order->platform_commission) {
                throw new \Exception('Insufficient balance. Your balance: Rp ' . number_format($driver->balance, 0, ',', '.') . 
                                   ', Required commission: Rp ' . number_format($order->platform_commission, 0, ',', '.') . 
                                   '. Please top up your balance.');
            }

            // Update order
            $order->update([
                'driver_id' => $driver->id,
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            // Update driver status to busy
            $driver->update([
                'status' => 'busy',
                'last_active_at' => now(),
            ]);

            DB::commit();

            // Send notifications
            $this->sendStatusNotification($order->fresh(), 'accepted');

            Log::info('Order accepted successfully', [
                'order_id' => $order->id,
                'driver_id' => $driver->id
            ]);

            return [
                'success' => true,
                'data' => $order->fresh()->load(['customer', 'driver']),
                'message' => 'Order accepted successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Accept Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'driver_id' => $driver->id,
                'driver_status' => $driver->status ?? 'unknown',
                'driver_is_verified' => $driver->is_verified ?? 'unknown',
                'driver_is_online' => $driver->is_online ?? 'unknown',
                'driver_balance' => $driver->balance ?? 'unknown',
                'required_commission' => $order->platform_commission ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to be caught by controller
        }
    }
}
