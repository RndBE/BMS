<?php

use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\RoomMarkerController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnergyController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (with optional floor filter)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/floor/{floor}', [DashboardController::class, 'byFloor'])->name('dashboard.floor');

    // Analisa Data
    Route::get('/analisa-data', [AnalysisController::class, 'index'])->name('analisa-data.index');

    // Energi
    Route::get('/energi', [EnergyController::class, 'index'])->name('energi.index');

    // Pengaturan
    Route::get('/pengaturan/umum', [PengaturanController::class, 'umum'])->name('pengaturan.umum');
    Route::post('/pengaturan/umum', [PengaturanController::class, 'umumSave'])->name('pengaturan.umum.save');
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

    // Room detail API for AJAX tooltip click
    Route::get('/api/rooms/{id}', [DashboardController::class, 'roomDetail'])->name('rooms.detail');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin Routes (superadmin only) ──────────────────────────────────────────
Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {

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
