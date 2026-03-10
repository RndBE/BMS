<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Sensor;
use App\Models\SensorReading;
use Illuminate\Database\Seeder;

class SensorReadingSeeder extends Seeder
{
    public function run(): void
    {
        // Realistic data per room
        $roomData = [
            'LBY' => ['temperature' => 24.5, 'humidity' => 50, 'co2' => 450],
            'HRD' => ['temperature' => 25.1, 'humidity' => 52, 'co2' => 480],
            'FIN' => ['temperature' => 24.8, 'humidity' => 49, 'co2' => 460],
            'DIR' => ['temperature' => 31.0, 'humidity' => 65, 'co2' => 1100], // poor
            'TLT' => ['temperature' => 26.0, 'humidity' => 70, 'co2' => 500],
            'IT'  => ['temperature' => 23.5, 'humidity' => 45, 'co2' => 420],
            'SWR' => ['temperature' => 27.3, 'humidity' => 58, 'co2' => 920], // warning
            'MTG' => ['temperature' => 28.9, 'humidity' => 55, 'co2' => 850], // warning
            'MKT' => ['temperature' => 25.5, 'humidity' => 51, 'co2' => 470],
            'PRD' => ['temperature' => 25.0, 'humidity' => 53, 'co2' => 490],
            'GDG' => ['temperature' => 26.5, 'humidity' => 60, 'co2' => 510],
        ];

        $rooms = Room::with('sensors')->get();

        foreach ($rooms as $room) {
            $data = $roomData[$room->code] ?? [];
            foreach ($room->sensors as $sensor) {
                if (isset($data[$sensor->type])) {
                    SensorReading::create([
                        'sensor_id'   => $sensor->id,
                        'value'       => $data[$sensor->type],
                        'recorded_at' => now()->subMinutes(rand(1, 60)),
                    ]);
                }
            }
        }
    }
}
