<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DriverDashboardController;
use App\Http\Controllers\Web\CustomerDashboardController;
use App\Http\Controllers\Web\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest fare calculation
Route::post('/calculate-fare', [CustomerDashboardController::class, 'calculateFare'])->name('calculate.fare');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth', 'web'])->group(function () {
    
    // General dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Customer dashboard routes
    Route::middleware('role:customer')->prefix('customer')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
        Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('customer.profile');
        Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('customer.profile.update');
        Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('customer.orders');
        Route::post('/calculate-fare', [CustomerDashboardController::class, 'calculateFare'])->name('calculate-fare');
        Route::get('/orders/{order}', [CustomerDashboardController::class, 'orderDetail'])->name('customer.orders.show');
        Route::post('/orders', [CustomerDashboardController::class, 'createOrder'])->name('customer.orders.create');
        Route::patch('/orders/{order}/cancel', [CustomerDashboardController::class, 'cancelOrder'])->name('customer.orders.cancel');
        Route::post('/orders/{order}/rate', [CustomerDashboardController::class, 'rateOrder'])->name('customer.orders.rate');
        Route::get('/book-ride', [CustomerDashboardController::class, 'bookRide'])->name('customer.book');
        Route::get('/find-drivers', [CustomerDashboardController::class, 'findDrivers'])->name('customer.drivers');
    });
    
    // Driver dashboard routes
    Route::middleware('role:driver')->prefix('driver')->group(function () {
        Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');
        Route::get('/profile', [DriverDashboardController::class, 'profile'])->name('driver.profile');
        Route::put('/profile', [DriverDashboardController::class, 'updateProfile'])->name('driver.profile.update');
        Route::get('/orders', [DriverDashboardController::class, 'orders'])->name('driver.orders');
        Route::get('/orders/{order}', [DriverDashboardController::class, 'orderDetail'])->name('driver.orders.show');
        Route::get('/available-orders', [DriverDashboardController::class, 'availableOrders'])->name('driver.available.orders');
        Route::get('/debug-orders', [DriverController::class, 'debugOrders']);
        Route::put('/orders/{order}/accept', [DriverDashboardController::class, 'acceptOrder'])->name('driver.orders.accept');
        Route::patch('/orders/{order}/update-status', [DriverDashboardController::class, 'updateOrderStatus'])->name('driver.orders.update-status');
        Route::get('/earnings', [DriverDashboardController::class, 'earnings'])->name('driver.earnings');
        Route::get('/documents', [DriverDashboardController::class, 'documents'])->name('driver.documents');
        Route::post('/documents', [DriverDashboardController::class, 'uploadDocument'])->name('driver.documents.upload');
        Route::put('/status', [DriverDashboardController::class, 'updateStatus'])->name('driver.status.update');
        Route::put('/location', [DriverDashboardController::class, 'updateLocation'])->name('driver.location.update');
    });
    
    // Admin dashboard routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        
        // User Management
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('admin.users');
        Route::get('/users/{user}', [AdminDashboardController::class, 'userDetail'])->name('admin.users.show');
        Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('admin.users.delete');
        Route::put('/users/{user}/status', [AdminDashboardController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
        
        // Driver Management
        Route::get('/drivers', [AdminDashboardController::class, 'drivers'])->name('admin.drivers');
        Route::get('/drivers/{driver}', [AdminDashboardController::class, 'driverDetail'])->name('admin.drivers.show');
        Route::put('/drivers/{driver}', [AdminDashboardController::class, 'updateDriver'])->name('admin.drivers.update');
        Route::put('/drivers/{driver}/status', [AdminDashboardController::class, 'toggleDriverStatus'])->name('admin.drivers.toggle-status');
        Route::put('/drivers/{driver}/approve', [AdminDashboardController::class, 'approveDriver'])->name('admin.drivers.approve');
        Route::put('/drivers/{driver}/reject', [AdminDashboardController::class, 'rejectDriver'])->name('admin.drivers.reject');
        Route::get('/drivers/{driver}/documents', [AdminDashboardController::class, 'driverDocuments'])->name('admin.drivers.documents');
        Route::put('/drivers/{driver}/documents/{document}/approve', [AdminDashboardController::class, 'approveDocument'])->name('admin.drivers.documents.approve');
        Route::put('/drivers/{driver}/documents/{document}/reject', [AdminDashboardController::class, 'rejectDocument'])->name('admin.drivers.documents.reject');
        
        // Order Management
        Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('admin.orders');
        Route::get('/orders/{order}', [AdminDashboardController::class, 'orderDetail'])->name('admin.orders.show');
        Route::put('/orders/{order}/status', [AdminDashboardController::class, 'updateOrderStatus'])->name('admin.orders.status');
        Route::delete('/orders/{order}', [AdminDashboardController::class, 'deleteOrder'])->name('admin.orders.delete');
        
        // Financial Management
        Route::get('/finances', [AdminDashboardController::class, 'finances'])->name('admin.finances');
        Route::get('/finances/drivers/{driver}', [AdminDashboardController::class, 'driverFinances'])->name('admin.finances.driver');
        Route::post('/finances/drivers/{driver}/payout', [AdminDashboardController::class, 'processPayout'])->name('admin.finances.payout');
        Route::get('/finances/reports', [AdminDashboardController::class, 'financialReports'])->name('admin.finances.reports');
        
        // Vehicle Type Management
        Route::get('/vehicle-types', [AdminDashboardController::class, 'vehicleTypes'])->name('admin.vehicle-types');
        Route::post('/vehicle-types', [AdminDashboardController::class, 'createVehicleType'])->name('admin.vehicle-types.create');
        Route::put('/vehicle-types/{vehicleType}', [AdminDashboardController::class, 'updateVehicleType'])->name('admin.vehicle-types.update');
        Route::delete('/vehicle-types/{vehicleType}', [AdminDashboardController::class, 'deleteVehicleType'])->name('admin.vehicle-types.delete');
        
        // Settings & Configuration
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
        Route::put('/settings', [AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');
        Route::get('/system-info', [AdminDashboardController::class, 'systemInfo'])->name('admin.system-info');
        
        // Reports & Analytics
        Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('admin.reports');
        Route::get('/reports/orders', [AdminDashboardController::class, 'orderReports'])->name('admin.reports.orders');
        Route::get('/reports/drivers', [AdminDashboardController::class, 'driverReports'])->name('admin.reports.drivers');
        Route::get('/reports/customers', [AdminDashboardController::class, 'customerReports'])->name('admin.reports.customers');
        Route::get('/reports/financial', [AdminDashboardController::class, 'financialReports'])->name('admin.reports.financial');
        
        // Notifications
        Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('admin.notifications');
        Route::post('/notifications/send', [AdminDashboardController::class, 'sendNotification'])->name('admin.notifications.send');
        Route::post('/notifications/broadcast', [AdminDashboardController::class, 'broadcastNotification'])->name('admin.notifications.broadcast');
    });
});

// Fallback untuk redirect berdasarkan role
Route::get('/home', function () {
    $user = auth()->user();
    
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'driver') {
        return redirect()->route('driver.dashboard');
    } elseif ($user->role === 'customer') {
        return redirect()->route('customer.dashboard');
    } else {
        return redirect()->route('home')->with('error', 'Role tidak valid.');
    }
})->middleware('auth')->name('home');

// Redirect common paths to API info
Route::get('/docs', function () {
    return redirect('/api/info');
});

Route::get('/api-docs', function () {
    return redirect('/api/info');
});

// Health check for web
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'environment' => app()->environment(),
        'api_endpoint' => url('/api')
    ]);
});
