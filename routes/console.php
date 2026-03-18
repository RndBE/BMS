<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled Commands ─────────────────────────────────────────────────────
// Cek sensor readings terhadap alert rules setiap menit
Schedule::command('alert:check')->everyMinute();

// Update status ruangan (online/offline) setiap menit
Schedule::command('rooms:update-status')->everyMinute();
