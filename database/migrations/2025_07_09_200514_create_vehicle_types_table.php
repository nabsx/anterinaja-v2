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
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // motorcycle, car, etc
            $table->string('display_name'); // Motor, Mobil
            $table->integer('base_fare'); // Tarif dasar
            $table->integer('per_km_rate'); // Tarif per km
            $table->integer('capacity'); // Kapasitas penumpang/barang
            $table->string('icon')->nullable(); // Icon kendaraan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }
};
