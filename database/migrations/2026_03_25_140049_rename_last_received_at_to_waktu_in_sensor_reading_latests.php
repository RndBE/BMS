<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_reading_latests', function (Blueprint $table) {
            $table->renameColumn('last_received_at', 'waktu');
        });
    }

    public function down(): void
    {
        Schema::table('sensor_reading_latests', function (Blueprint $table) {
            $table->renameColumn('waktu', 'last_received_at');
        });
    }
};
