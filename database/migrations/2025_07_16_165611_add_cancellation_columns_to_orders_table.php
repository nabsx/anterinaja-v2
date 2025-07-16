<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add cancellation-related columns if they don't exist
            if (!Schema::hasColumn('orders', 'cancelled_by')) {
                $table->string('cancelled_by')->nullable()->after('cancelled_at');
            }
            
            if (!Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_by');
            }
            
            // Add other missing columns that might be needed
            if (!Schema::hasColumn('orders', 'driver_earning')) {
                $table->integer('driver_earning')->nullable()->after('fare_amount');
            }
            
            if (!Schema::hasColumn('orders', 'platform_commission')) {
                $table->integer('platform_commission')->nullable()->after('driver_earning');
            }
            
            if (!Schema::hasColumn('orders', 'fare_breakdown')) {
                $table->json('fare_breakdown')->nullable()->after('platform_commission');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'cancelled_by',
                'cancellation_reason',
                'driver_earning',
                'platform_commission',
                'fare_breakdown'
            ]);
        });
    }
};
