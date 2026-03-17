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

        // Satu sensor per ruangan (sensor multi-parameter sudah dihandle oleh SensorParameter)
        $rooms = Room::all();

        foreach ($rooms as $room) {
            // Cek apakah room sudah punya sensor, skip jika sudah ada
            if (Sensor::where('room_id', $room->id)->exists()) {
                continue;
            }

            Sensor::create([
                'room_id'         => $room->id,
                'sensor_group_id' => $groups['SG-TEMP'] ?? ($groups->first() ?? null),
                'tipe_sensor'     => 'Multi-Parameter Sensor',
                'is_active'       => true,
            ]);
        }
    }
}
