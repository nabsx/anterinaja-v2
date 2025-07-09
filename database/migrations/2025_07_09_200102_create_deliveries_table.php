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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('item_description'); // Deskripsi barang
            $table->decimal('item_weight', 5, 2)->nullable(); // Berat barang (kg)
            $table->string('recipient_name'); // Nama penerima
            $table->string('recipient_phone'); // Telepon penerima
            $table->text('special_instructions')->nullable(); // Instruksi khusus
            $table->string('item_photo')->nullable(); // Foto barang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
