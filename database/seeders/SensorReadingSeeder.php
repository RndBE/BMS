<?php

namespace Database\Seeders;

use App\Models\Room;
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
     * ║  $dateFrom         : tanggal mulai  (null = auto dari $days) ║
     * ║  $dateTo           : tanggal selesai (null = hari ini)       ║
     * ║  $days             : berapa hari ke belakang                 ║
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

    private int     $intervalMinutes = 1;
    private ?string $dateFrom        = null;
    private ?string $dateTo          = null;
    private int     $days            = 7;

    // Batas nilai per kolom sensor (min, max, decimals)
    private array $limits = [
        'sensor1'  => ['min' =>     18.0, 'max' =>    40.0, 'decimals' => 1],   // Suhu °C
        'sensor2'  => ['min' =>     20.0, 'max' =>    90.0, 'decimals' => 1],   // Kelembaban %
        'sensor3'  => ['min' =>      0.0, 'max' =>   200.0, 'decimals' => 2],   // Energi kWh
        'sensor4'  => ['min' =>      0.0, 'max' =>  5000.0, 'decimals' => 1],   // Daya W
        'sensor5'  => ['min' =>    350.0, 'max' =>  2000.0, 'decimals' => 0],   // CO₂ ppm
        'sensor6'  => ['min' =>    900.0, 'max' =>  1100.0, 'decimals' => 1],   // Tekanan hPa
        'sensor7'  => ['min' =>      0.0, 'max' => 10000.0, 'decimals' => 0],   // Cahaya lux
        'sensor8'  => ['min' =>      0.0, 'max' =>    20.0, 'decimals' => 1],   // Angin m/s
        'sensor9'  => ['min' =>      0.0, 'max' =>   500.0, 'decimals' => 1],   // PM2.5 µg/m³
        'sensor10' => ['min' =>      0.0, 'max' =>   600.0, 'decimals' => 1],   // PM10 µg/m³
        'sensor11' => ['min' =>      0.0, 'max' =>  1000.0, 'decimals' => 0],   // VOC ppb
        'sensor12' => ['min' =>     30.0, 'max' =>   120.0, 'decimals' => 1],   // Noise dB
        'sensor13' => ['min' =>    200.0, 'max' =>   240.0, 'decimals' => 1],   // Tegangan V
        'sensor14' => ['min' =>      0.0, 'max' =>    32.0, 'decimals' => 2],   // Arus A
        'sensor15' => ['min' =>     49.0, 'max' =>    51.0, 'decimals' => 2],   // Frekuensi Hz
        'sensor16' => ['min' =>      0.1, 'max' =>     1.0, 'decimals' => 2],   // Power Factor
    ];

    public function run(): void
    {
        $from = Carbon::create(2026, 2, 1,  0,  0, 0);
        $to   = Carbon::create(2026, 3, 17, 16,  0, 0);

        $totalMinutes = (int) $from->diffInMinutes($to);
        $totalPoints  = (int) ($totalMinutes / $this->intervalMinutes);

        $rooms  = Room::all();
        $insert = [];

        foreach ($rooms as $room) {
            for ($i = 0; $i <= $totalPoints; $i++) {
                $waktu = $from->copy()->addMinutes($i * $this->intervalMinutes);

                $row = [
                    'room_id' => $room->id,
                    'waktu'   => $waktu->toDateTimeString(),
                ];

                // Isi sensor1 – sensor16 dengan nilai random sesuai range
                foreach ($this->limits as $kolom => $cfg) {
                    $row[$kolom] = $this->randBetween($cfg['min'], $cfg['max'], $cfg['decimals']);
                }

                $insert[] = $row;

                // Batch insert tiap 500 baris agar tidak out of memory
                if (count($insert) >= 500) {
                    SensorReading::insert($insert);
                    $insert = [];
                }
            }
        }

        if (!empty($insert)) {
            SensorReading::insert($insert);
        }
    }

    private function randBetween(float $min, float $max, int $decimals = 1): float
    {
        $factor = 10 ** $decimals;
        return round(mt_rand((int)($min * $factor), (int)($max * $factor)) / $factor, $decimals);
    }
}
