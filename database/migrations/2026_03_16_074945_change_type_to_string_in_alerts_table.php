<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah ENUM ke string agar bisa menampung tipe baru dari alert rules
        DB::statement("ALTER TABLE alerts MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'warning'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE alerts MODIFY COLUMN type ENUM('sensor_offline','high_temp','ac_off','high_power')");
    }
};
