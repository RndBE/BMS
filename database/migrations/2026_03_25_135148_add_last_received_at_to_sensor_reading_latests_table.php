<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_reading_latests', function (Blueprint $table) {
            // Waktu server menerima data (untuk cek offline: > 60 menit → offline)
            $table->timestamp('last_received_at')->nullable()->after('recorded_at');
        });

        // Backfill: isi dengan updated_at yang sudah ada
        DB::statement('UPDATE sensor_reading_latests SET last_received_at = updated_at');
    }

    public function down(): void
    {
        Schema::table('sensor_reading_latests', function (Blueprint $table) {
            $table->dropColumn('last_received_at');
        });
    }
};
