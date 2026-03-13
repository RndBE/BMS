<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SensorReadingSeeder extends Seeder
{
    public function run(): void
    {
        // Nilai baseline per ruangan
        $roomData = [
            'LBY' => ['temperature' => 24.5, 'humidity' => 50.0, 'energy' => 12.4,  'power' => 850.0,   'co2' => 650.0],
            'HRD' => ['temperature' => 25.1, 'humidity' => 52.0, 'energy' => 18.7,  'power' => 1200.0,  'co2' => 700.0],
            'FIN' => ['temperature' => 24.8, 'humidity' => 49.0, 'energy' => 15.2,  'power' => 980.0,   'co2' => 680.0],
            'DIR' => ['temperature' => 31.0, 'humidity' => 65.0, 'energy' => 22.1,  'power' => 2100.0,  'co2' => 900.0],
            'TLT' => ['temperature' => 26.0, 'humidity' => 70.0, 'energy' => 8.3,   'power' => 420.0,   'co2' => 500.0],
            'IT'  => ['temperature' => 23.5, 'humidity' => 45.0, 'energy' => 45.6,  'power' => 3800.0,  'co2' => 620.0],
            'SWR' => ['temperature' => 27.3, 'humidity' => 58.0, 'energy' => 19.8,  'power' => 1650.0,  'co2' => 750.0],
            'MTG' => ['temperature' => 28.9, 'humidity' => 55.0, 'energy' => 16.4,  'power' => 1400.0,  'co2' => 850.0],
            'MKT' => ['temperature' => 25.5, 'humidity' => 51.0, 'energy' => 14.9,  'power' => 1050.0,  'co2' => 710.0],
            'PRD' => ['temperature' => 25.0, 'humidity' => 53.0, 'energy' => 21.3,  'power' => 1750.0,  'co2' => 780.0],
            'GDG' => ['temperature' => 26.5, 'humidity' => 60.0, 'energy' => 9.7,   'power' => 630.0,   'co2' => 580.0],
        ];

        // Rentang fluktuasi random per tipe (±)
        $fluctuation = [
            'temperature' => 1.5,    // ±1.5 °C
            'humidity'    => 5.0,    // ±5 %
            'energy'      => 2.0,    // ±2 kWh
            'power'       => 150.0,  // ±150 W
            'co2'         => 50.0,   // ±50 ppm
        ];

        $intervalMinutes = 1;                                           // data setiap 5 menit
        $days            = 7;                                           // 7 hari ke belakang
        $totalPoints     = (int)(($days * 24 * 60) / $intervalMinutes); // 2016 titik per ruangan

        $rooms  = Room::all();
        $insert = [];

        foreach ($rooms as $room) {
            $baseline = $roomData[$room->code] ?? null;
            if (!$baseline) continue;

            // State nilai saat ini per tipe sensor (random walk)
            $current = $baseline;

            for ($i = $totalPoints; $i >= 0; $i--) {
                $waktu = Carbon::now()->subMinutes($i * $intervalMinutes);
                $hour  = $waktu->hour;

                // Jam sibuk (08:00–17:00): nilai sedikit lebih tinggi
                $workBoost = ($hour >= 8 && $hour < 17) ? 0.3 : 0;

                foreach (['temperature', 'humidity', 'energy', 'power', 'co2'] as $type) {
                    $fluc           = $fluctuation[$type];
                    $delta          = (mt_rand(-100, 100) / 100) * $fluc + ($fluc * $workBoost);
                    $current[$type] = round($current[$type] + $delta, 4);

                    // Clamp agar tidak keluar batas wajar
                    $current[$type] = match ($type) {
                        'temperature' => max(18.0,  min(40.0,    $current[$type])),
                        'humidity'    => max(20.0,  min(90.0,    $current[$type])),
                        'energy'      => max(0.0,   min(200.0,   $current[$type])),
                        'power'       => max(0.0,   min(10000.0, $current[$type])),
                        'co2'         => max(350.0, min(2000.0,  $current[$type])),
                        default       => $current[$type],
                    };
                }

                $insert[] = [
                    'room_id'     => $room->id,
                    'temperature' => $current['temperature'],
                    'humidity'    => $current['humidity'],
                    'energy'      => $current['energy'],
                    'power'       => $current['power'],
                    'co2'         => $current['co2'],
                    'waktu'       => $waktu->toDateTimeString(),
                ];

                // Batch insert per 500 baris agar tidak out of memory
                if (count($insert) >= 500) {
                    SensorReading::insert($insert);
                    $insert = [];
                }
            }
        }

        // Insert sisa batch
        if (!empty($insert)) {
            SensorReading::insert($insert);
        }
    }
}

