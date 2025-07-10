<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Rating;
use App\Services\OrderService;
use App\Services\FareCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $orderService;
    protected $fareCalculationService;

    public function __construct(OrderService $orderService, FareCalculationService $fareCalculationService)
    {
        $this->orderService = $orderService;
        $this->fareCalculationService = $fareCalculationService;
    }

    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $status = $request->query('status');
            $limit = $request->query('limit', 20);

            if ($user->role === 'driver') {
                $result = $this->orderService->getDriverOrderHistory($user->driver->id, $limit, $status);
            } else {
                $result = $this->orderService->getUserOrderHistory($user->id, $limit, $status);
            }

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'count' => $result['count']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new order
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_type' => 'required|in:ride,delivery',
                'pickup_address' => 'required|string|max:255',
                'pickup_lat' => 'required|numeric|between:-90,90',
                'pickup_lng' => 'required|numeric|between:-180,180',
                'destination_address' => 'required|string|max:255',
                'destination_lat' => 'required|numeric|between:-90,90',
                'destination_lng' => 'required|numeric|between:-180,180',
                'vehicle_type' => 'sometimes|in:motorcycle,car',
                'notes' => 'sometimes|string|max:500',
                'scheduled_at' => 'sometimes|date|after:now',
                
                // Delivery specific fields
                'item_description' => 'required_if:order_type,delivery|string|max:255',
                'item_weight' => 'sometimes|numeric|min:0|max:100',
                'recipient_name' => 'required_if:order_type,delivery|string|max:255',
                'recipient_phone' => 'required_if:order_type,delivery|string|max:20',
                'special_instructions' => 'sometimes|string|max:500',
                'item_photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Only customers can create orders
            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only customers can create orders'
                ], 403);
            }

            $orderData = $request->only([
                'order_type', 'pickup_address', 'pickup_lat', 'pickup_lng',
                'destination_address', 'destination_lat', 'destination_lng',
                'vehicle_type', 'notes', 'scheduled_at'
            ]);

            $result = $this->orderService->createOrder($user->id, $orderData);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            $order = $result['data'];

            // Create delivery details if order type is delivery
            if ($request->order_type === 'delivery') {
                $deliveryData = [
                    'order_id' => $order->id,
                    'item_description' => $request->item_description,
                    'item_weight' => $request->item_weight,
                    'recipient_name' => $request->recipient_name,
                    'recipient_phone' => $request->recipient_phone,
                    'special_instructions' => $request->special_instructions,
                ];

                // Handle item photo upload
                if ($request->hasFile('item_photo')) {
                    $path = $request->file('item_photo')->store('delivery_photos', 'public');
                    $deliveryData['item_photo'] = $path;
                }

                Delivery::create($deliveryData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->fresh()->load(['delivery', 'trackings'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order details
     */
    public function show(Request $request, $id)
    {
        try {
            $result = $this->orderService->getOrderDetails($id);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 404);
            }

            $order = $result['data'];
            $user = $request->user();

            // Check if user has access to this order
            if ($user->role === 'customer' && $order->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            if ($user->role === 'driver' && $order->driver_id !== $user->driver->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $order->load(['delivery', 'trackings', 'ratings'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get order details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $cancelledBy = $user->role;

            $result = $this->orderService->cancelOrder($id, $request->reason, $cancelledBy);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rate order (for completed orders)
     */
    public function rate(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'sometimes|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $order = Order::findOrFail($id);

            // Check if order is completed
            if ($order->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only rate completed orders'
                ], 400);
            }

            // Check if user has access to this order
            if ($user->role === 'customer' && $order->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            if ($user->role === 'driver' && $order->driver_id !== $user->driver->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Check if already rated
            $existingRating = Rating::where('order_id', $id)
                ->where('rated_by', $user->role)
                ->first();

            if ($existingRating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order already rated'
                ], 400);
            }

            // Create rating
            $ratingData = [
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'driver_id' => $order->driver_id,
                'rated_by' => $user->role,
                'rating' => $request->rating,
                'review' => $request->review,
            ];

            $rating = Rating::create($ratingData);

            // Update driver rating if customer rated
            if ($user->role === 'customer') {
                $order->driver->updateRating();
            }

            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'data' => $rating
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit rating',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}