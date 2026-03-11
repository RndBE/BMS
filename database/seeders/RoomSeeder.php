<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // ViewBox: 0 0 900 560
        // Left diagonal cluster (along angled exterior wall)
        // Inner office block: x=228–660, y=52–345 (top row y=52–183, corridor y=183–209, bottom y=209–345)
        // Warehouse: x=662–852, y=52–345
        $rooms = [
            // Left diagonal cluster
            ['name' => 'Ruang HRD',       'code' => 'HRD', 'svg_x' => 15,  'svg_y' => 78,  'svg_width' => 88, 'svg_height' => 65, 'status' => 'normal'],
            ['name' => 'Ruang Finance',   'code' => 'FIN', 'svg_x' => 12,  'svg_y' => 153, 'svg_width' => 88, 'svg_height' => 65, 'status' => 'normal'],
            ['name' => 'Ruang Direksi',   'code' => 'DIR', 'svg_x' => 8,   'svg_y' => 228, 'svg_width' => 85, 'svg_height' => 65, 'status' => 'poor'],
            ['name' => 'Toilet',          'code' => 'TLT', 'svg_x' => 5,   'svg_y' => 303, 'svg_width' => 82, 'svg_height' => 52, 'status' => 'normal'],
            // Inner block — top row
            ['name' => 'Lobby',           'code' => 'LBY', 'svg_x' => 230, 'svg_y' => 54,  'svg_width' => 105, 'svg_height' => 129, 'status' => 'normal'],
            ['name' => 'Ruang IT',        'code' => 'IT',  'svg_x' => 337, 'svg_y' => 54,  'svg_width' => 113, 'svg_height' => 129, 'status' => 'normal'],
            ['name' => 'Ruang Software',  'code' => 'SWR', 'svg_x' => 452, 'svg_y' => 54,  'svg_width' => 104, 'svg_height' => 129, 'status' => 'warning'],
            ['name' => 'Ruang Marketing', 'code' => 'MKT', 'svg_x' => 558, 'svg_y' => 54,  'svg_width' => 100, 'svg_height' => 129, 'status' => 'normal'],
            // Inner block — bottom row
            ['name' => 'Ruang Meeting',   'code' => 'MTG', 'svg_x' => 230, 'svg_y' => 211, 'svg_width' => 228, 'svg_height' => 132, 'status' => 'warning'],
            ['name' => 'Ruang Produksi',  'code' => 'PRD', 'svg_x' => 460, 'svg_y' => 211, 'svg_width' => 198, 'svg_height' => 132, 'status' => 'normal'],
            // Right warehouse
            ['name' => 'Ruang Gudang',    'code' => 'GDG', 'svg_x' => 662, 'svg_y' => 54,  'svg_width' => 190, 'svg_height' => 289, 'status' => 'normal'],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
