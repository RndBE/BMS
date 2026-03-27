<?php

namespace Database\Seeders;

use App\Models\AlertLimit;
use App\Models\Room;
use App\Models\SensorParameter;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SensorReadingSeeder extends Seeder
{
    /**
     * ╔══════════════════════════════════════════════════════════════╗
     * ║           KONFIGURASI SEEDER — Ubah di sini                 ║
     * ╠══════════════════════════════════════════════════════════════╣
     * ║  $intervalMinutes  : jarak antar titik data (menit)         ║
     * ╚══════════════════════════════════════════════════════════════╝
     *
     * Pemetaan default kolom:
     *   sensor1  → Suhu           (°C)    18–40
     *   sensor2  → Kelembaban     (%)     20–90
     *   sensor3  → Energi         (kWh)   0–200
     *   sensor4  → Daya           (W)     0–5000
     *   sensor5  → CO₂            (ppm)   350–2000
     *   sensor6  → Tekanan        (hPa)   900–1100
     *   sensor7  → Cahaya         (lux)   0–10000
     *   sensor8  → Kecepatan Angin(m/s)   0–20
     *   sensor9  → PM2.5          (µg/m³) 0–500
     *   sensor10 → PM10           (µg/m³) 0–600
     *   sensor11 → VOC            (ppb)   0–1000
     *   sensor12 → Noise          (dB)    30–120
     *   sensor13 → Tegangan       (V)     200–240
     *   sensor14 → Arus           (A)     0–32
     *   sensor15 → Frekuensi      (Hz)    49–51
     *   sensor16 → Power Factor   (-)     0.1–1.0
     */

    private int $intervalMinutes = 1;

    // Batas nilai fallback per kolom sensor (jika tidak ada AlertLimit)
    private array $limits = [
        'sensor1'  => ['min' =>     18.0, 'max' =>    40.0, 'decimals' => 1],
        'sensor2'  => ['min' =>     20.0, 'max' =>    90.0, 'decimals' => 1],
        'sensor3'  => ['min' =>      0.0, 'max' =>   200.0, 'decimals' => 2],
        'sensor4'  => ['min' =>      0.0, 'max' =>  5000.0, 'decimals' => 1],
        'sensor5'  => ['min' =>    350.0, 'max' =>  2000.0, 'decimals' => 0],
        'sensor6'  => ['min' =>    900.0, 'max' =>  1100.0, 'decimals' => 1],
        'sensor7'  => ['min' =>      0.0, 'max' => 10000.0, 'decimals' => 0],
        'sensor8'  => ['min' =>      0.0, 'max' =>    20.0, 'decimals' => 1],
        'sensor9'  => ['min' =>      0.0, 'max' =>   500.0, 'decimals' => 1],
        'sensor10' => ['min' =>      0.0, 'max' =>   600.0, 'decimals' => 1],
        'sensor11' => ['min' =>      0.0, 'max' =>  1000.0, 'decimals' => 0],
        'sensor12' => ['min' =>     30.0, 'max' =>   120.0, 'decimals' => 1],
        'sensor13' => ['min' =>    200.0, 'max' =>   240.0, 'decimals' => 1],
        'sensor14' => ['min' =>      0.0, 'max' =>    32.0, 'decimals' => 2],
        'sensor15' => ['min' =>     49.0, 'max' =>    51.0, 'decimals' => 2],
        'sensor16' => ['min' =>      0.1, 'max' =>     1.0, 'decimals' => 2],
    ];

    // Distribusi zona: normal 60%, warning 28%, poor 12%
    private array $zoneDist = ['normal', 'normal', 'normal', 'normal', 'normal', 'normal',
                                'warning', 'warning', 'warning',
                                'poor', 'poor', 'poor'];

    public function run(): void
    {
        $from = Carbon::create(2026, 2, 1,  0,  0, 0);
        $to   = Carbon::create(2026, 3, 27, 14, 00, 00);

        $totalMinutes = (int) $from->diffInMinutes($to);
        $totalPoints  = (int) ($totalMinutes / $this->intervalMinutes);

        $rooms = Room::all();

        // ── Load AlertLimit per parameter_key ────────────────────────────────────
        $alertLimits = AlertLimit::all()->keyBy('parameter_key');

        // ── $keyMap identik dengan SensorReadingObserver ─────────────────────────
        $keyMap = [
            'suhu'        => 'suhu',
            'temperature' => 'suhu',
            'kelembaban'  => 'kelembaban',
            'humidity'    => 'kelembaban',
            'co2'         => 'co2',
            'daya'        => 'daya',
            'energi'      => 'energi',
        ];

        // ── Load SensorParameter: map room_id → [kolom_reading => parameter_key] ─
        $roomParamMap = [];
        SensorParameter::whereNotNull('kolom_reading')
            ->whereNotNull('nama_parameter')
            ->get(['room_id', 'kolom_reading', 'nama_parameter'])
            ->each(function ($p) use (&$roomParamMap, $keyMap) {
                $paramKey = null;
                foreach ($keyMap as $pattern => $key) {
                    if (stripos($p->nama_parameter, $pattern) !== false) {
                        $paramKey = $key;
                        break;
                    }
                }
                if ($paramKey) {
                    $roomParamMap[$p->room_id][$p->kolom_reading] = $paramKey;
                }
            });

        $insert = [];

        foreach ($rooms as $room) {
            for ($i = 0; $i <= $totalPoints; $i++) {
                $waktu = $from->copy()->addMinutes($i * $this->intervalMinutes);

                $row = [
                    'room_id' => $room->id,
                    'waktu'   => $waktu->toDateTimeString(),
                ];

                foreach ($this->limits as $kolom => $cfg) {
                    $paramKey = $roomParamMap[$room->id][$kolom] ?? null;
                    $alert    = $paramKey ? ($alertLimits[$paramKey] ?? null) : null;

                    $row[$kolom] = $alert
                        ? $this->randByZone($alert, $cfg)
                        : $this->randBetween($cfg['min'], $cfg['max'], $cfg['decimals']);
                }

                $insert[] = $row;

                if (count($insert) >= 500) {
                    SensorReading::insert($insert);
                    $insert = [];
                }
            }
        }

        if (!empty($insert)) {
            SensorReading::insert($insert);
        }

        // ── Backfill sensor_reading_latests ──────────────────────────────────────
        // ::insert() bypass Eloquent observer, jadi perlu backfill manual
        \Illuminate\Support\Facades\DB::statement("
            INSERT INTO sensor_reading_latests
                (room_id,
                 sensor1,  sensor2,  sensor3,  sensor4,
                 sensor5,  sensor6,  sensor7,  sensor8,
                 sensor9,  sensor10, sensor11, sensor12,
                 sensor13, sensor14, sensor15, sensor16,
                 recorded_at, waktu, updated_at)
            SELECT
                sr.room_id,
                sr.sensor1,  sr.sensor2,  sr.sensor3,  sr.sensor4,
                sr.sensor5,  sr.sensor6,  sr.sensor7,  sr.sensor8,
                sr.sensor9,  sr.sensor10, sr.sensor11, sr.sensor12,
                sr.sensor13, sr.sensor14, sr.sensor15, sr.sensor16,
                sr.waktu, sr.waktu, NOW()
            FROM sensor_readings sr
            INNER JOIN (
                SELECT room_id, MAX(waktu) AS max_waktu
                FROM sensor_readings GROUP BY room_id
            ) latest ON sr.room_id = latest.room_id AND sr.waktu = latest.max_waktu
            ON DUPLICATE KEY UPDATE
                sensor1  = VALUES(sensor1),  sensor2  = VALUES(sensor2),
                sensor3  = VALUES(sensor3),  sensor4  = VALUES(sensor4),
                sensor5  = VALUES(sensor5),  sensor6  = VALUES(sensor6),
                sensor7  = VALUES(sensor7),  sensor8  = VALUES(sensor8),
                sensor9  = VALUES(sensor9),  sensor10 = VALUES(sensor10),
                sensor11 = VALUES(sensor11), sensor12 = VALUES(sensor12),
                sensor13 = VALUES(sensor13), sensor14 = VALUES(sensor14),
                sensor15 = VALUES(sensor15), sensor16 = VALUES(sensor16),
                recorded_at = VALUES(recorded_at),
                waktu       = VALUES(waktu),
                updated_at  = VALUES(updated_at)
        ");
    }

    /**
     * Generate nilai dalam zona normal/warning/poor secara acak
     * berdasarkan distribusi zoneDist (60% normal, 28% warning, 12% poor)
     */
    private function randByZone(AlertLimit $alert, array $cfg): float
    {
        $dec  = $cfg['decimals'];
        $zone = $this->zoneDist[array_rand($this->zoneDist)];

        return match ($zone) {
            'normal'  => $this->randBetween(
                max($cfg['min'], (float)($alert->normal_min ?? $cfg['min'])),
                min($cfg['max'], (float)($alert->normal_max ?? $cfg['max'])),
                $dec
            ),
            'warning' => (mt_rand(0, 1) === 0)
                // Warning LOW
                ? $this->randBetween(
                    max($cfg['min'], (float)($alert->warn_low_min ?? $cfg['min'])),
                    min($cfg['max'], (float)($alert->warn_low_max ?? $alert->normal_min ?? $cfg['max'])),
                    $dec
                )
                // Warning HIGH
                : $this->randBetween(
                    max($cfg['min'], (float)($alert->warn_high_min ?? $alert->normal_max ?? $cfg['min'])),
                    min($cfg['max'], (float)($alert->warn_high_max ?? $cfg['max'])),
                    $dec
                ),
            'poor'    => (mt_rand(0, 1) === 0)
                // Poor LOW
                ? $this->randBetween(
                    $cfg['min'],
                    min($cfg['max'], (float)($alert->poor_low ?? $alert->warn_low_min ?? $cfg['min'])),
                    $dec
                )
                // Poor HIGH
                : $this->randBetween(
                    max($cfg['min'], (float)($alert->poor_high ?? $alert->warn_high_max ?? $cfg['max'])),
                    $cfg['max'],
                    $dec
                ),
            default   => $this->randBetween($cfg['min'], $cfg['max'], $dec),
        };
    }

    private function randBetween(float $min, float $max, int $decimals = 1): float
    {
        if ($min >= $max) return round($min, $decimals);
        $factor = 10 ** $decimals;
        return round(mt_rand((int)($min * $factor), (int)($max * $factor)) / $factor, $decimals);
    }
}
