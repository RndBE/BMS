<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alert_rules', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('name');
            $table->unsignedInteger('durasi_tunda')->nullable()->after('notification_channel'); // minutes
            $table->json('room_ids')->nullable()->after('durasi_tunda'); // array of room IDs
        });
    }

    public function down(): void
    {
        Schema::table('alert_rules', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'durasi_tunda', 'room_ids']);
        });
    }
};
