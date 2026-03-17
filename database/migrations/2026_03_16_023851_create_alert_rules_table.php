<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // Nama aturan peringatan
            $table->string('parameter_key');              // sensor parameter key, e.g. 'suhu'
            $table->enum('condition', ['>', '<', '>=', '<=', '==', '!=']); // kondisi
            $table->float('threshold');                   // nilai ambang
            $table->enum('severity', ['warning', 'critical'])->default('warning');
            $table->string('notification_channel')->nullable(); // whatsapp, email, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_rules');
    }
};
