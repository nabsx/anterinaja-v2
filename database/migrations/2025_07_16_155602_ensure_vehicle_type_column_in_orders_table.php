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
            // Check if vehicle_type column doesn't exist and add it
            if (!Schema::hasColumn('orders', 'vehicle_type')) {
                $table->enum('vehicle_type', ['motorcycle', 'car', 'van', 'truck'])
                      ->default('car')
                      ->after('destination_longitude');
            }
            
            // Ensure order_type has correct values
            if (Schema::hasColumn('orders', 'order_type')) {
                // Update existing enum if needed
                $table->enum('order_type', ['ride', 'delivery'])
                      ->default('ride')
                      ->change();
            } else {
                $table->enum('order_type', ['ride', 'delivery'])
                      ->default('ride')
                      ->after('driver_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'vehicle_type')) {
                $table->dropColumn('vehicle_type');
            }
        });
    }
};
