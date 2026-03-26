<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_reading_latests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')
                  ->unique()                    // 1 baris per ruangan
                  ->constrained('rooms')
                  ->onDelete('cascade');

            // Mirror sensor1–sensor16 dari sensor_readings
            for ($i = 1; $i <= 16; $i++) {
                $table->decimal("sensor{$i}", 10, 4)->nullable();
            }

            $table->timestamp('recorded_at')->nullable(); // waktu data sensor asli
            $table->timestamp('updated_at')->nullable();  // kapan baris ini diupdate
        });

        // ── Backfill: ambil baris terbaru (MAX waktu) per room ─────────────────
        DB::statement("
            INSERT INTO sensor_reading_latests
                (room_id,
                 sensor1,  sensor2,  sensor3,  sensor4,
                 sensor5,  sensor6,  sensor7,  sensor8,
                 sensor9,  sensor10, sensor11, sensor12,
                 sensor13, sensor14, sensor15, sensor16,
                 recorded_at, updated_at)
            SELECT
                sr.room_id,
                sr.sensor1,  sr.sensor2,  sr.sensor3,  sr.sensor4,
                sr.sensor5,  sr.sensor6,  sr.sensor7,  sr.sensor8,
                sr.sensor9,  sr.sensor10, sr.sensor11, sr.sensor12,
                sr.sensor13, sr.sensor14, sr.sensor15, sr.sensor16,
                sr.waktu, NOW()
            FROM sensor_readings sr
            INNER JOIN (
                SELECT room_id, MAX(waktu) AS max_waktu
                FROM sensor_readings
                GROUP BY room_id
            ) latest ON sr.room_id = latest.room_id AND sr.waktu = latest.max_waktu
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_reading_latests');
    }
};
