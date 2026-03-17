<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->foreignId('alert_rule_id')->nullable()->constrained('alert_rules')->nullOnDelete()->after('room_id');
            $table->float('nilai')->nullable()->after('alert_rule_id'); // nilai sensor yang memicu alert
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropForeign(['alert_rule_id']);
            $table->dropColumn(['alert_rule_id', 'nilai']);
        });
    }
};
