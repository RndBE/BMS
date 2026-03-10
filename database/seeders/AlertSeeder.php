<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Room;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        $alerts = [
            ['code' => 'MTG', 'type' => 'sensor_offline', 'message' => 'Sensor Offline',   'minutes_ago' => 0],
            ['code' => 'SWR', 'type' => 'high_temp',      'message' => 'Suhu Tinggi',       'minutes_ago' => 30],
            ['code' => 'DIR', 'type' => 'ac_off',         'message' => 'AC Mati',           'minutes_ago' => 30],
            ['code' => 'PRD', 'type' => 'high_power',     'message' => 'Daya Tinggi',       'minutes_ago' => 60],
            ['code' => 'GDG', 'type' => 'high_temp',      'message' => 'Suhu Tinggi',       'minutes_ago' => 90],
            ['code' => 'FIN', 'type' => 'sensor_offline', 'message' => 'Sensor Offline',    'minutes_ago' => 120],
        ];

        foreach ($alerts as $alert) {
            $room = Room::where('code', $alert['code'])->first();
            if ($room) {
                Alert::create([
                    'room_id'    => $room->id,
                    'type'       => $alert['type'],
                    'message'    => $alert['message'],
                    'is_read'    => false,
                    'created_at' => now()->subMinutes($alert['minutes_ago']),
                    'updated_at' => now()->subMinutes($alert['minutes_ago']),
                ]);
            }
        }
    }
}
