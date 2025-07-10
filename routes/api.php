<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\FareController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public fare estimation (no auth required)
Route::get('/fare/estimate', [FareController::class, 'estimate']);
Route::get('/fare/rates', [FareController::class, 'rates']);
Route::get('/fare/surge', [FareController::class, 'surge']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/driver', [ProfileController::class, 'updateDriverProfile']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/rate', [OrderController::class, 'rate']);

    // Customer specific routes
    Route::prefix('customer')->group(function () {
        Route::get('/profile', [CustomerController::class, 'profile']);
        Route::get('/calculate-fare', [CustomerController::class, 'calculateFare']);
        Route::get('/nearby-drivers', [CustomerController::class, 'findNearbyDrivers']);
        Route::get('/statistics', [CustomerController::class, 'statistics']);
    });

    // Driver specific routes
    Route::prefix('driver')->group(function () {
        Route::get('/profile', [DriverController::class, 'profile']);
        Route::put('/location', [DriverController::class, 'updateLocation']);
        Route::put('/online-status', [DriverController::class, 'setOnlineStatus']);
        Route::get('/available-orders', [DriverController::class, 'availableOrders']);
        Route::put('/orders/{orderId}/accept', [DriverController::class, 'acceptOrder']);
        Route::put('/orders/{orderId}/status', [DriverController::class, 'updateOrderStatus']);
        Route::post('/documents', [DriverController::class, 'uploadDocument']);
        Route::get('/statistics', [DriverController::class, 'statistics']);
    });
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// API documentation info
Route::get('/info', function () {
    return response()->json([
        'app_name' => config('app.name'),
        'version' => '1.0.0',
        'environment' => app()->environment(),
        'endpoints' => [
            'auth' => [
                'POST /api/register',
                'POST /api/login',
                'POST /api/logout',
                'GET /api/me',
                'POST /api/refresh',
                'POST /api/change-password'
            ],
            'orders' => [
                'GET /api/orders',
                'POST /api/orders',
                'GET /api/orders/{id}',
                'PUT /api/orders/{id}/cancel',
                'POST /api/orders/{id}/rate'
            ],
            'fare' => [
                'GET /api/fare/estimate',
                'GET /api/fare/rates',
                'GET /api/fare/surge'
            ],
            'customer' => [
                'GET /api/customer/profile',
                'GET /api/customer/calculate-fare',
                'GET /api/customer/nearby-drivers',
                'GET /api/customer/statistics'
            ],
            'driver' => [
                'GET /api/driver/profile',
                'PUT /api/driver/location',
                'PUT /api/driver/online-status',
                'GET /api/driver/available-orders',
                'PUT /api/driver/orders/{orderId}/accept',
                'PUT /api/driver/orders/{orderId}/status',
                'POST /api/driver/documents',
                'GET /api/driver/statistics'
            ]
        ]
    ]);
});
