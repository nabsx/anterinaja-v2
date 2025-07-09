<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambahkan index ke tabel orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['pickup_latitude', 'pickup_longitude']);
            $table->index(['created_at']);
        });

        // Tambahkan index ke tabel drivers
        Schema::table('drivers', function (Blueprint $table) {
            $table->index(['current_latitude', 'current_longitude']);
            $table->index(['is_online', 'status']);
        });
    }

    public function down(): void
    {
        // Hapus index dari tabel orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['pickup_latitude', 'pickup_longitude']);
            $table->dropIndex(['created_at']);
        });

        // Hapus index dari tabel drivers
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['current_latitude', 'current_longitude']);
            $table->dropIndex(['is_online', 'status']);
        });
    }
};
