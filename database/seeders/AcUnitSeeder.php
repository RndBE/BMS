<?php

namespace Database\Seeders;

use App\Models\AcUnit;
use App\Models\Room;
use Illuminate\Database\Seeder;

class AcUnitSeeder extends Seeder
{
    public function run(): void
    {
        // 9 active out of 11 rooms (sesuai screenshot "9/11 Unit AC Aktif")
        $acData = [
            'LBY' => ['is_active' => true,  'power_kw' => 1.5],
            'HRD' => ['is_active' => true,  'power_kw' => 1.0],
            'FIN' => ['is_active' => true,  'power_kw' => 1.0],
            'DIR' => ['is_active' => false, 'power_kw' => 0.0], // poor → AC off
            'TLT' => ['is_active' => true,  'power_kw' => 0.5],
            'IT'  => ['is_active' => true,  'power_kw' => 2.0],
            'SWR' => ['is_active' => true,  'power_kw' => 1.5],
            'MTG' => ['is_active' => false, 'power_kw' => 0.0], // AC off
            'MKT' => ['is_active' => true,  'power_kw' => 1.0],
            'PRD' => ['is_active' => true,  'power_kw' => 2.0],
            'GDG' => ['is_active' => true,  'power_kw' => 1.0],
        ];

        $rooms = Room::all();
        foreach ($rooms as $room) {
            $data = $acData[$room->code] ?? ['is_active' => false, 'power_kw' => 0];
            AcUnit::create([
                'room_id'   => $room->id,
                'name'      => 'AC ' . $room->name,
                'is_active' => $data['is_active'],
                'power_kw'  => $data['power_kw'],
            ]);
        }
    }
}
