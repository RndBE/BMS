<?php

use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\RoomMarkerController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnergyController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PeringatanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard — semua user login bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/floor/{floor}', [DashboardController::class, 'byFloor'])->name('dashboard.floor');
    Route::get('/api/rooms/{id}', [DashboardController::class, 'roomDetail'])->name('rooms.detail');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Analisa Data ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:lihat_analisa'])->group(function () {
    Route::get('/analisa-data', [AnalysisController::class, 'index'])->name('analisa-data.index');
});

// ── Energi ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:lihat_energi'])->group(function () {
    Route::get('/energi', [EnergyController::class, 'index'])->name('energi.index');
});

// ── Log Peringatan ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:lihat_log'])->group(function () {
    Route::get('/log-peringatan', [PeringatanController::class, 'logIndex'])->name('log-peringatan.index');
    // Mark individual alert as read (saat buka detail)
    Route::patch('/log-peringatan/{alert}/read', [PeringatanController::class, 'logMarkRead'])->name('log-peringatan.mark-read');
    // Tandai semua dibaca
    Route::patch('/log-peringatan/read-all', [PeringatanController::class, 'logMarkAllRead'])->name('log-peringatan.read-all');
});

// ── Kelola Pengaturan (Umum) ──────────────────────────────────────────────────
Route::middleware(['auth', 'permission:kelola_pengaturan'])->group(function () {
    Route::get('/pengaturan/umum', [PengaturanController::class, 'umum'])->name('pengaturan.umum');
    Route::post('/pengaturan/umum', [PengaturanController::class, 'umumSave'])->name('pengaturan.umum.save');
});

// ── Kelola Konfigurasi (Ruangan, Sensor, AC) ──────────────────────────────────
Route::middleware(['auth', 'permission:kelola_konfigurasi'])->group(function () {
    Route::get('/pengaturan/konfigurasi', [PengaturanController::class, 'konfigurasi'])->name('pengaturan.konfigurasi');
    Route::post('/pengaturan/rooms', [PengaturanController::class, 'roomStore'])->name('pengaturan.rooms.store');
    Route::put('/pengaturan/rooms/{room}', [PengaturanController::class, 'roomUpdate'])->name('pengaturan.rooms.update');
    Route::delete('/pengaturan/rooms/{room}', [PengaturanController::class, 'roomDestroy'])->name('pengaturan.rooms.destroy');
    Route::get('/pengaturan/sensors/{sensor}', [PengaturanController::class, 'sensorShow'])->name('pengaturan.sensors.show');
    Route::post('/pengaturan/sensors', [PengaturanController::class, 'sensorStore'])->name('pengaturan.sensors.store');
    Route::put('/pengaturan/sensors/{sensor}', [PengaturanController::class, 'sensorUpdate'])->name('pengaturan.sensors.update');
    Route::delete('/pengaturan/sensors/{sensor}', [PengaturanController::class, 'sensorDestroy'])->name('pengaturan.sensors.destroy');
    Route::post('/pengaturan/acunits', [PengaturanController::class, 'acStore'])->name('pengaturan.acunits.store');
    Route::put('/pengaturan/acunits/{acUnit}', [PengaturanController::class, 'acUpdate'])->name('pengaturan.acunits.update');
    Route::delete('/pengaturan/acunits/{acUnit}', [PengaturanController::class, 'acDestroy'])->name('pengaturan.acunits.destroy');
    Route::patch('/pengaturan/acunits/{acUnit}/toggle', [PengaturanController::class, 'acToggle'])->name('pengaturan.acunits.toggle');
});

// ── Kelola Peringatan (Batas Normal, Aturan, Log) ─────────────────────────────
Route::middleware(['auth', 'permission:kelola_peringatan'])->group(function () {
    Route::get('/pengaturan/peringatan', [PeringatanController::class, 'index'])->name('pengaturan.peringatan');
    Route::post('/pengaturan/peringatan/batas-normal', [PeringatanController::class, 'batasNormalSave'])->name('pengaturan.peringatan.batas-normal.save');
    Route::post('/pengaturan/peringatan/batas-normal/reset', [PeringatanController::class, 'batasNormalReset'])->name('pengaturan.peringatan.batas-normal.reset');
    Route::post('/pengaturan/peringatan/rules', [PeringatanController::class, 'rulesStore'])->name('pengaturan.peringatan.rules.store');
    Route::put('/pengaturan/peringatan/rules/{alertRule}', [PeringatanController::class, 'rulesUpdate'])->name('pengaturan.peringatan.rules.update');
    Route::delete('/pengaturan/peringatan/rules/{alertRule}', [PeringatanController::class, 'rulesDestroy'])->name('pengaturan.peringatan.rules.destroy');
    Route::patch('/pengaturan/peringatan/rules/{alertRule}/toggle', [PeringatanController::class, 'rulesToggle'])->name('pengaturan.peringatan.rules.toggle');
    Route::patch('/pengaturan/peringatan/log/{alert}/read', [PeringatanController::class, 'logMarkRead'])->name('pengaturan.peringatan.log.read');
    Route::patch('/pengaturan/peringatan/log/read-all', [PeringatanController::class, 'logMarkAllRead'])->name('pengaturan.peringatan.log.read-all');
    Route::delete('/pengaturan/peringatan/log/{alert}', [PeringatanController::class, 'logDestroy'])->name('pengaturan.peringatan.log.destroy');
    Route::delete('/pengaturan/peringatan/log', [PeringatanController::class, 'logClear'])->name('pengaturan.peringatan.log.clear');
});

// ── Kelola Pengguna (User, Role, Permission) ──────────────────────────────────
Route::middleware(['auth', 'permission:kelola_pengguna'])->group(function () {
    Route::get('/pengaturan/pengguna', [PenggunaController::class, 'index'])->name('pengaturan.pengguna');
    Route::post('/pengaturan/pengguna/users', [PenggunaController::class, 'userStore'])->name('pengaturan.pengguna.users.store');
    Route::put('/pengaturan/pengguna/users/{user}', [PenggunaController::class, 'userUpdate'])->name('pengaturan.pengguna.users.update');
    Route::delete('/pengaturan/pengguna/users/{user}', [PenggunaController::class, 'userDestroy'])->name('pengaturan.pengguna.users.destroy');
    Route::post('/pengaturan/pengguna/roles', [PenggunaController::class, 'roleStore'])->name('pengaturan.pengguna.roles.store');
    Route::put('/pengaturan/pengguna/roles/{role}', [PenggunaController::class, 'roleUpdate'])->name('pengaturan.pengguna.roles.update');
    Route::delete('/pengaturan/pengguna/roles/{role}', [PenggunaController::class, 'roleDestroy'])->name('pengaturan.pengguna.roles.destroy');
    Route::post('/pengaturan/pengguna/permissions', [PenggunaController::class, 'permissionStore'])->name('pengaturan.pengguna.permissions.store');
    Route::put('/pengaturan/pengguna/permissions/{permission}', [PenggunaController::class, 'permissionUpdate'])->name('pengaturan.pengguna.permissions.update');
    Route::delete('/pengaturan/pengguna/permissions/{permission}', [PenggunaController::class, 'permissionDestroy'])->name('pengaturan.pengguna.permissions.destroy');
});


// ─── Admin Routes (permission-based) ─────────────────────────────────────────
Route::middleware(['auth', 'permission:kelola_denah'])->prefix('admin')->name('admin.')->group(function () {

    // Buildings CRUD
    Route::get('buildings', [BuildingController::class, 'index'])->name('buildings.index');
    Route::post('buildings', [BuildingController::class, 'store'])->name('buildings.store');
    Route::put('buildings/{building}', [BuildingController::class, 'update'])->name('buildings.update');
    Route::delete('buildings/{building}', [BuildingController::class, 'destroy'])->name('buildings.destroy');

    // Floors
    Route::post('floors', [FloorController::class, 'store'])->name('floors.store');
    Route::get('floors/{floor}/editor', [FloorController::class, 'show'])->name('floors.show');
    Route::put('floors/{floor}', [FloorController::class, 'update'])->name('floors.update');
    Route::delete('floors/{floor}', [FloorController::class, 'destroy'])->name('floors.destroy');
    Route::post('floors/{floor}/upload', [FloorController::class, 'uploadPlan'])->name('floors.upload');
    Route::post('floors/{floor}/rooms', [FloorController::class, 'addRoom'])->name('floors.rooms.add');

    // Room marker position (AJAX)
    Route::put('rooms/{room}/marker', [RoomMarkerController::class, 'update'])->name('rooms.marker.update');
    Route::put('rooms/{room}/details', [RoomMarkerController::class, 'updateDetails'])->name('rooms.details.update');
    Route::delete('rooms/{room}', [RoomMarkerController::class, 'destroy'])->name('rooms.destroy');

    // Floor canvas drawing save
    Route::post('floors/{floor}/canvas', [FloorController::class, 'saveCanvas'])->name('floors.canvas.save');
});

require __DIR__.'/auth.php';
