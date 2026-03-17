<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_limits', function (Blueprint $table) {
            $table->id();
            $table->string('parameter_key')->unique(); // e.g. 'suhu', 'kelembaban', 'co2', 'daya', 'tegangan'
            $table->string('label');                   // e.g. 'Suhu (°C)'
            $table->string('icon')->nullable();        // emoji/icon identifier
            // Normal band
            $table->float('normal_min')->nullable();
            $table->float('normal_max')->nullable();
            // Warning band (two-sided: low-side min/max, high-side min/max)
            $table->float('warn_low_min')->nullable();
            $table->float('warn_low_max')->nullable();
            $table->float('warn_high_min')->nullable();
            $table->float('warn_high_max')->nullable();
            // Poor band (threshold value, direction: '<' or '>')
            $table->float('poor_low')->nullable();     // < this value = poor (low side)
            $table->float('poor_high')->nullable();    // > this value = poor (high side)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_limits');
    }
};
