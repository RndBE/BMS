<?php

namespace App\Observers;

use App\Models\Alert;
use App\Models\AlertLimit;
use App\Models\Room;
use App\Models\SensorParameter;
use App\Models\SensorReading;
use App\Models\SensorReadingLatest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
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
                'recorded_at' => $reading->waktu,
                'waktu'       => $reading->waktu,
                'updated_at'  => now(),
            ])
        );

        // ── 2. Ambil sensor parameters room ini (di-cache 60 detik) ──────────
        $params = Cache::remember("sensor_params_{$roomId}", 60, function () use ($roomId) {
            return SensorParameter::where('room_id', $roomId)
                ->whereNotNull('kolom_reading')
                ->get(['nama_parameter', 'kolom_reading']);
        });

        // ── 3. Hitung status room berdasarkan AlertLimit ──────────────────────
        $limits = Cache::remember('alert_limits_all', 60, fn () =>
            AlertLimit::all()->keyBy('parameter_key')
        );

        $keyMap = [
            'suhu'        => 'suhu',
            'temperature' => 'suhu',
            'kelembaban'  => 'kelembaban',
            'humidity'    => 'kelembaban',
            'co2'         => 'co2',
            'daya'        => 'daya',
            'energi'      => 'energi',
        ];

        $worstStatus = 'normal';

        foreach ($params as $param) {
            $kolom = $param->kolom_reading;
            $nilai = (float) ($reading->{$kolom} ?? 0);

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

            $isPoor = ($lim->poor_low  !== null && $nilai < $lim->poor_low)
                   || ($lim->poor_high !== null && $nilai > $lim->poor_high);

            if ($isPoor) {
                $worstStatus = 'poor';
                break;
            }

            $isWarning = ($lim->warn_low_min  !== null && $nilai >= $lim->warn_low_min  && $nilai <= ($lim->warn_low_max  ?? PHP_FLOAT_MAX))
                      || ($lim->warn_high_min !== null && $nilai >= $lim->warn_high_min && $nilai <= ($lim->warn_high_max ?? PHP_FLOAT_MAX));

            if ($isWarning && $worstStatus !== 'poor') {
                $worstStatus = 'warning';
            }
        }

        // Update status room jika berubah (hindari write tidak perlu)
        Room::where('id', $roomId)
            ->where('status', '!=', $worstStatus)
            ->update(['status' => $worstStatus, 'updated_at' => now()]);

        // ── 5. Alert sensor kembali online ───────────────────────────────────────
        // Jika ada alert 'sensor_offline' yang belum dibalas dengan 'sensor_online',
        // buat alert sekali bahwa sensor sudah aktif kembali.
        $lastOfflineAt = Alert::where('room_id', $roomId)
            ->where('type', 'sensor_offline')
            ->orderByDesc('created_at')
            ->value('created_at');

        if ($lastOfflineAt) {
            $alreadyOnline = Alert::where('room_id', $roomId)
                ->where('type', 'sensor_online')
                ->where('created_at', '>=', $lastOfflineAt)
                ->exists();

            if (! $alreadyOnline) {
                $roomName = Room::find($roomId)?->name ?? "Room #{$roomId}";
                Alert::create([
                    'room_id'       => $roomId,
                    'alert_rule_id' => null,
                    'type'          => 'sensor_online',
                    'message'       => "Sensor {$roomName} kembali online",
                    'nilai'         => null,
                    'is_read'       => false,
                ]);
            }
        }

        // ── 6. Trigger pengecekan AlertRule → Log Peringatan ─────────────────
        Artisan::call('alert:check');
    }
}
