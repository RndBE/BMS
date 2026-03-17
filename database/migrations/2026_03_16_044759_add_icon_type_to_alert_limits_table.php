<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alert_limits', function (Blueprint $table) {
            $table->string('icon_type')->default('emoji')->after('icon'); // 'img' or 'emoji'
        });
    }

    public function down(): void
    {
        Schema::table('alert_limits', function (Blueprint $table) {
            $table->dropColumn('icon_type');
        });
    }
};
