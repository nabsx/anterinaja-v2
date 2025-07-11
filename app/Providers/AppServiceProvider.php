<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OSRMService::class, function ($app) {
            return new OSRMService();
        });
        
        $this->app->bind(FareCalculationService::class, function ($app) {
            return new FareCalculationService();
        });
        
        $this->app->bind(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(OSRMService::class),
                $app->make(FareCalculationService::class),
                $app->make(NotificationService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        date_default_timezone_set('Asia/Jakarta'); // Waktu PHP (Laravel)
    
    // Waktu MySQL
    DB::statement("SET time_zone = '+07:00'");
    }
}
