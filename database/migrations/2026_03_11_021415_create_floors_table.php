<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
            $table->string('name');           // "Lantai 1", "Basement", dll
            $table->integer('floor_number')->default(1);
            $table->string('plan_file_path')->nullable(); // storage path
            $table->enum('plan_file_type', ['image', 'svg', 'pdf'])->nullable();
            $table->unsignedSmallInteger('plan_width')->default(1200);  // canvas width px
            $table->unsignedSmallInteger('plan_height')->default(800);  // canvas height px
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};
