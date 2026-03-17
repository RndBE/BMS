<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            // Dashboard query: latest() = ORDER BY created_at DESC LIMIT 4
            $table->index('created_at', 'idx_alerts_created_at');
            // Log query: is_read filter
            $table->index('is_read', 'idx_alerts_is_read');
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropIndex('idx_alerts_created_at');
            $table->dropIndex('idx_alerts_is_read');
        });
    }
};
