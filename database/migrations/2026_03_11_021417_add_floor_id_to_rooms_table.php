<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('floor_id')->nullable()->after('id')->constrained('floors')->nullOnDelete();
            // marker position on the canvas (percentage-based for responsiveness)
            $table->decimal('marker_x', 7, 4)->nullable()->after('floor_id'); // 0-100%
            $table->decimal('marker_y', 7, 4)->nullable()->after('marker_x'); // 0-100%
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['floor_id']);
            $table->dropColumn(['floor_id', 'marker_x', 'marker_y']);
        });
    }
};
