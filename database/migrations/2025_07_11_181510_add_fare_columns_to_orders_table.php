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
            // Tambahkan kolom actual_fare dan platform_commission
            $table->bigInteger('actual_fare')->nullable()->after('status');
            $table->bigInteger('platform_commission')->nullable()->after('actual_fare');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('actual_fare');
            $table->dropColumn('platform_commission');
        });
    }
};
