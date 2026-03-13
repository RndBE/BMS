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
            Sensor::create(['room_id' => $room->id, 'type' => 'energy',      'unit' => 'kWh', 'is_active' => true]);
            Sensor::create(['room_id' => $room->id, 'type' => 'power',       'unit' => 'W',   'is_active' => true]);
        }
    }
}
