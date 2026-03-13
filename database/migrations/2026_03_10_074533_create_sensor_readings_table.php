<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->decimal('temperature', 8, 4)->nullable();
            $table->decimal('humidity', 8, 4)->nullable();
            $table->decimal('energy', 10, 4)->nullable();
            $table->decimal('power', 10, 4)->nullable();
            $table->decimal('co2', 8, 2)->nullable();
            $table->timestamp('waktu')->useCurrent();

            // ── Index untuk query realtime ──────────────────────────────────────
            // 1. Composite ASC: WHERE room_id = ? ORDER BY waktu ASC (time-series chart)
            $table->index(['room_id', 'waktu'], 'idx_room_waktu_asc');

            // 2. Standalone waktu: filter rentang waktu global tanpa room_id
            $table->index('waktu', 'idx_waktu');
        });

        // 3. Composite DESC — harus pakai raw SQL agar MySQL optimizer bisa pakai
        //    untuk: WHERE room_id = ? ORDER BY waktu DESC LIMIT 1
        DB::statement(
            'ALTER TABLE sensor_readings ADD INDEX idx_room_waktu_desc (room_id, waktu DESC)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
