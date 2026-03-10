<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('svg_x')->default(0);
            $table->integer('svg_y')->default(0);
            $table->integer('svg_width')->default(100);
            $table->integer('svg_height')->default(80);
            $table->enum('status', ['normal', 'warning', 'poor'])->default('normal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
