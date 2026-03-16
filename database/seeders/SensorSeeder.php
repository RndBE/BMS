<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Sensor;
use App\Models\SensorGroup;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil sensor groups berdasarkan kode
        $groups = SensorGroup::pluck('id', 'kode_sensor');

        $typeMap = [
            'SG-TEMP' => ['type' => 'temperature', 'unit' => '°C'],
            'SG-HUM'  => ['type' => 'humidity',    'unit' => '%'],
            'SG-ENER' => ['type' => 'energy',      'unit' => 'kWh'],
            'SG-PWR'  => ['type' => 'power',       'unit' => 'W'],
        ];

        $rooms = Room::all();

        foreach ($rooms as $room) {
            foreach ($typeMap as $kode => $meta) {
                Sensor::create([
                    'room_id'         => $room->id,
                    'sensor_group_id' => $groups[$kode] ?? null,
                    'type'            => $meta['type'],
                    'unit'            => $meta['unit'],
                    'is_active'       => true,
                ]);
            }
        }
    }
}
