<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Urutan tampil ruangan di denah / konfigurasi
            $table->unsignedInteger('sort_order')->default(0)->after('status');
            // Status aktif/nonaktif ruangan (1 = aktif, 0 = nonaktif)
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'is_active']);
        });
    }
};
