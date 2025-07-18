<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE drivers MODIFY COLUMN status ENUM('available', 'busy', 'offline', 'rejected')");
    }
    
    public function down()
    {
    // Ubah data yang tidak valid dulu
    DB::table('drivers')->where('status', 'rejected')->update(['status' => 'offline']);

    // Baru rollback enum-nya
    DB::statement("ALTER TABLE drivers MODIFY COLUMN status ENUM('available', 'busy', 'offline')");
    }
};
