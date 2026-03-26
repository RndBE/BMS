<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\SensorReading;
use App\Models\SensorReadingLatest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SensorDataController extends Controller
{
    /**
     * Terima data sensor dari perangkat/logger.
     *
     * Format payload:
     * {
     *   "id_alat": "30001",           ← harus cocok dengan rooms.code
     *   "jam":  "11:33:00",
     *   "hari": "2026-03-13",
     *   "sensor1":  { "nama": "...", "nilai": 27.5, "satuan": "C" },
     *   "sensor2":  { "nama": "...", "nilai": 65.2, "satuan": "%" },
     *   ...
     *   "sensor16": { ... }            ← sensor yang kosong: {} diabaikan
     * }
     *
     * POST /api/sensor-data
     */
    public function store(Request $request)
    {
        // ── 1. Validasi field wajib ──────────────────────────────────────────
        $idAlat = $request->input('id_alat');
        $jam    = $request->input('jam');
        $hari   = $request->input('hari');

        if (!$idAlat || !$jam || !$hari) {
            return response()->json([
                'success' => false,
                'message' => 'Field id_alat, jam, dan hari wajib diisi.',
            ], 422);
        }

        // ── 2. Parse waktu ──────────────────────────────────────────────────
        try {
            $waktu = Carbon::createFromFormat('Y-m-d H:i:s', "{$hari} {$jam}", config('app.timezone', 'Asia/Jakarta'))
                ->setTimezone(config('app.timezone', 'Asia/Jakarta'));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Format hari/jam tidak valid. Gunakan hari: Y-m-d, jam: H:i:s',
            ], 422);
        }

        // ── 3. Cari room berdasarkan id_alat = rooms.code ───────────────────
        $room = Room::where('code', $idAlat)->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => "Ruangan dengan kode '{$idAlat}' tidak ditemukan.",
            ], 404);
        }

        // ── 4. Ekstrak nilai sensor (sensor1 … sensor16) ────────────────────
        $row = [
            'room_id' => $room->id,
            'waktu'   => $waktu,
        ];

        for ($i = 1; $i <= 16; $i++) {
            $key  = 'sensor' . $i;
            $data = $request->input($key);

            // Jika dikirim sebagai x-www-form-urlencoded, sensor berupa string JSON
            // → decode dulu agar bisa diakses sebagai array
            if (is_string($data) && str_starts_with(trim($data), '{')) {
                $data = json_decode($data, true);
            }

            // Sensor bisa berupa array {nama, nilai, satuan} atau empty {} atau null
            if (is_array($data) && array_key_exists('nilai', $data) && $data['nilai'] !== null) {
                $row[$key] = (float) $data['nilai'];
            } else {
                $row[$key] = null;
            }
        }

        // ── 5. Simpan ke sensor_readings (trigger SensorReadingObserver) ────
        try {
            $reading = SensorReading::create($row);
        } catch (\Throwable $e) {
            Log::error('SensorDataController@store - DB error: ' . $e->getMessage(), [
                'id_alat' => $idAlat,
                'room_id' => $room->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data sensor.',
            ], 500);
        }

        // ── 6. Response ──────────────────────────────────────────────────────
        Log::info("Sensor data diterima", [
            'id_alat' => $idAlat,
            'room'    => $room->name,
            'waktu'   => $waktu->toDateTimeString(),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Data sensor berhasil disimpan.',
            'room'     => $room->name,
            'waktu'    => $waktu->toDateTimeString(),
            'reading_id' => $reading->id,
        ], 201);
    }
}
