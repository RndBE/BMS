<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel sensor_groups terlebih dahulu
        Schema::create('sensor_groups', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sensor')->unique();   // Contoh: SG-TEMP, SG-HUM
            $table->string('nama_sensor');             // Contoh: Sensor Suhu
            $table->text('deskripsi')->nullable();     // Deskripsi singkat sensor
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Update tabel sensors: tambah gambar, tipe_sensor, sensor_group_id
        Schema::table('sensors', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('room_id');       // Path gambar/icon sensor
            $table->string('tipe_sensor')->nullable()->after('gambar');   // Tipe sensor (string bebas)
            $table->foreignId('sensor_group_id')
                  ->nullable()
                  ->after('room_id')
                  ->constrained('sensor_groups')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Lepas FK dan hapus kolom dari sensors
        Schema::table('sensors', function (Blueprint $table) {
            $table->dropForeign(['sensor_group_id']);
            $table->dropColumn(['sensor_group_id', 'gambar', 'tipe_sensor']);
        });

        // Hapus tabel sensor_groups
        Schema::dropIfExists('sensor_groups');
    }
};
