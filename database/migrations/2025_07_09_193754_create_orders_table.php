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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // TAMBAH: Kode order unik
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade'); // UBAH: Lebih jelas
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->enum('order_type', ['ride', 'delivery']); // UBAH: Lebih jelas
            
            // Pickup location
            $table->string('pickup_address');
            $table->decimal('pickup_latitude', 10, 7);
            $table->decimal('pickup_longitude', 10, 7);
            
            // Destination location
            $table->string('destination_address');
            $table->decimal('destination_latitude', 10, 7);
            $table->decimal('destination_longitude', 10, 7);
            
            // Route & fare calculation
            $table->decimal('distance_km', 8, 2)->nullable(); // TAMBAH: Jarak dalam km
            $table->integer('estimated_duration')->nullable(); // TAMBAH: Estimasi durasi (menit)
            $table->integer('fare_amount')->default(0); // UBAH: Lebih jelas
            
            // Order details
            $table->text('notes')->nullable(); // UBAH: Catatan
            $table->enum('status', [
                'pending',
                'accepted', 
                'driver_arrived',
                'picked_up',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('pending'); // PERBAIKI: Status lebih lengkap
            
            // Timestamps
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->timestamps();
            
            // TAMBAH: Indexes untuk performance
            $table->index(['customer_id', 'status']);
            $table->index(['driver_id', 'status']);
            $table->index(['order_type', 'status']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
