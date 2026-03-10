<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Sensor;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            Sensor::create(['room_id' => $room->id, 'type' => 'temperature', 'unit' => '°C',  'is_active' => true]);
            Sensor::create(['room_id' => $room->id, 'type' => 'humidity',    'unit' => '%',   'is_active' => true]);
            Sensor::create(['room_id' => $room->id, 'type' => 'co2',         'unit' => 'ppm', 'is_active' => $room->status !== 'poor']);
        }
    }
}
