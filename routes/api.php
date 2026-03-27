<?php

use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\AlertNotifController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — BMS Sensor Data Ingestion
|--------------------------------------------------------------------------
|
| Endpoint tanpa autentikasi (sensor device kirim langsung).
| Gunakan API key middleware jika perlu keamanan tambahan.
|
*/

// ── Health check ────────────────────────────────────────────────────────────
Route::get('/ping', fn() => response()->json(['status' => 'ok', 'service' => 'BMS API']));

// ── Sensor Data Ingestion ───────────────────────────────────────────────────
// POST /api/sensor-data
// Body: { id_alat, jam, hari, sensor1:{nama,nilai,satuan}, ..., sensor16:{...} }
Route::post('/sensor-data', [SensorDataController::class, 'store']);

// ── Alert Notifications (butuh session/auth web) ────────────────────────────
Route::middleware('web')->group(function () {
    Route::get('/alerts/unread',            [AlertNotifController::class,  'unread']);
    Route::get('/dashboard/rooms-status',   [\App\Http\Controllers\DashboardController::class, 'roomsStatus']);
});
