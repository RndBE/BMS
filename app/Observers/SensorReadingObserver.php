<?php

namespace App\Observers;

use App\Models\AlertLimit;
use App\Models\Room;
use App\Models\SensorParameter;
use App\Models\SensorReading;
use App\Models\SensorReadingLatest;
use Illuminate\Support\Facades\Cache;

class SensorReadingObserver
{
    public function created(SensorReading $reading): void
    {
        $roomId = $reading->room_id;

        // ── 1. Upsert sensor_reading_latests ─────────────────────────────────
        $sensorCols = [];
        for ($i = 1; $i <= 16; $i++) {
            $col = "sensor{$i}";
            $sensorCols[$col] = $reading->{$col};
        }

        SensorReadingLatest::updateOrCreate(
            ['room_id' => $roomId],
            array_merge($sensorCols, [
                'recorded_at'     => $reading->waktu,
                'waktu'           => $reading->waktu,
                'updated_at'      => now(),
            ])
        );

        // ── 2. Hitung status baru berdasarkan AlertLimit ──────────────────────
        // Cache AlertLimit 60 detik agar tidak query DB setiap reading masuk
        $limits = Cache::remember('alert_limits_all', 60, fn () => AlertLimit::all()->keyBy('parameter_key'));

        // Mapping parameter_key → kolom reading (untuk room ini)
        // Ambil dari sensor_parameters yang sudah ada di room
        $params = Cache::remember("sensor_params_{$roomId}", 60, function () use ($roomId) {
            return SensorParameter::where('room_id', $roomId)
                ->whereNotNull('kolom_reading')
                ->get(['nama_parameter', 'kolom_reading']);
        });

        // parameter_key mapping: nama_parameter → key di AlertLimit
        $keyMap = [
            'suhu'        => 'suhu',
            'temperature' => 'suhu',
            'kelembaban'  => 'kelembaban',
            'humidity'    => 'kelembaban',
            'co2'         => 'co2',
            'daya'        => 'daya',
            'energi'      => 'energi',
        ];

        $worstStatus = 'normal'; // default

        foreach ($params as $param) {
            $kolom = $param->kolom_reading;              // e.g. "sensor1"
            $nilai = (float) ($reading->{$kolom} ?? 0);

            // cari parameter_key dari nama_parameter (case-insensitive)
            $paramKey = null;
            foreach ($keyMap as $pattern => $key) {
                if (stripos($param->nama_parameter, $pattern) !== false) {
                    $paramKey = $key;
                    break;
                }
            }

            if (! $paramKey || ! $limits->has($paramKey)) {
                continue;
            }

            $lim = $limits[$paramKey];

            // Cek poor
            $isPoor = ($lim->poor_low  !== null && $nilai < $lim->poor_low)
                   || ($lim->poor_high !== null && $nilai > $lim->poor_high);

            if ($isPoor) {
                $worstStatus = 'poor';
                break; // poor sudah worst-case, stop
            }

            // Cek warning
            $isWarning = ($lim->warn_low_min !== null && $nilai >= $lim->warn_low_min && $nilai <= ($lim->warn_low_max ?? PHP_FLOAT_MAX))
                      || ($lim->warn_high_min !== null && $nilai >= $lim->warn_high_min && $nilai <= ($lim->warn_high_max ?? PHP_FLOAT_MAX));

            if ($isWarning && $worstStatus !== 'poor') {
                $worstStatus = 'warning';
            }
        }

        // Update rooms.status hanya jika berubah (hindari unnecessary write)
        Room::where('id', $roomId)
            ->where('status', '!=', $worstStatus)
            ->update(['status' => $worstStatus, 'updated_at' => now()]);
    }
}
