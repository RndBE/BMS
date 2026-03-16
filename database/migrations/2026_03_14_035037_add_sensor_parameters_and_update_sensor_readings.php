<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Buat tabel sensor_parameters ─────────────────────────────────────
        Schema::create('sensor_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')
                  ->constrained('rooms')
                  ->onDelete('cascade');            // FK ke ruangan (bukan per sensor)
            $table->string('nama_parameter');       // Contoh: Suhu, Kelembaban, CO₂
            $table->string('unit')->nullable();     // Contoh: °C, %, ppm
            $table->string('kolom_reading')->nullable(); // Kolom di sensor_readings: sensor1–sensor16
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── 2. Update sensor_readings: hapus semua kolom lama, tambah sensor1–sensor16 ──
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropColumn(['temperature', 'humidity', 'energy', 'power', 'co2']);

            // Tambah sensor1–sensor16 berurutan (chained after agar urutan benar di DB)
            $table->decimal('sensor1',  10, 4)->nullable()->after('room_id');
            $table->decimal('sensor2',  10, 4)->nullable()->after('sensor1');
            $table->decimal('sensor3',  10, 4)->nullable()->after('sensor2');
            $table->decimal('sensor4',  10, 4)->nullable()->after('sensor3');
            $table->decimal('sensor5',  10, 4)->nullable()->after('sensor4');
            $table->decimal('sensor6',  10, 4)->nullable()->after('sensor5');
            $table->decimal('sensor7',  10, 4)->nullable()->after('sensor6');
            $table->decimal('sensor8',  10, 4)->nullable()->after('sensor7');
            $table->decimal('sensor9',  10, 4)->nullable()->after('sensor8');
            $table->decimal('sensor10', 10, 4)->nullable()->after('sensor9');
            $table->decimal('sensor11', 10, 4)->nullable()->after('sensor10');
            $table->decimal('sensor12', 10, 4)->nullable()->after('sensor11');
            $table->decimal('sensor13', 10, 4)->nullable()->after('sensor12');
            $table->decimal('sensor14', 10, 4)->nullable()->after('sensor13');
            $table->decimal('sensor15', 10, 4)->nullable()->after('sensor14');
            $table->decimal('sensor16', 10, 4)->nullable()->after('sensor15');
        });
    }

    public function down(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $cols = [];
            for ($i = 1; $i <= 16; $i++) $cols[] = "sensor{$i}";
            $table->dropColumn($cols);

            $table->decimal('temperature', 8, 4)->nullable()->after('room_id');
            $table->decimal('humidity',    8, 4)->nullable()->after('temperature');
            $table->decimal('energy',     10, 4)->nullable()->after('humidity');
            $table->decimal('power',      10, 4)->nullable()->after('energy');
            $table->decimal('co2',         8, 2)->nullable()->after('power');
        });

        Schema::dropIfExists('sensor_parameters');
    }
};
