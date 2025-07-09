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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('vehicle_type', ['motorcycle', 'car']); // TAMBAH: Jenis kendaraan
            $table->string('vehicle_plate')->unique(); // TAMBAH: Plat nomor
            $table->string('license_number')->unique(); // TAMBAH: Nomor SIM
            $table->boolean('is_verified')->default(false); // TAMBAH: Status verifikasi
            $table->decimal('current_latitude', 10, 7)->nullable(); // UBAH: Lebih jelas
            $table->decimal('current_longitude', 10, 7)->nullable(); // UBAH: Lebih jelas
            $table->boolean('is_online')->default(false);
            $table->enum('status', ['available', 'busy', 'offline'])->default('offline'); // TAMBAH: offline
            $table->decimal('rating', 3, 2)->default(5.00); // TAMBAH: Rating driver
            $table->integer('total_trips')->default(0); // TAMBAH: Total perjalanan
            $table->timestamp('last_active_at')->nullable(); // TAMBAH: Terakhir aktif
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
