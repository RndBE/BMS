<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->dropColumn(['type', 'unit']);
        });
    }

    public function down(): void
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->enum('type', ['temperature', 'humidity', 'energy', 'power'])->default('temperature');
            $table->string('unit')->nullable();
        });
    }
};
