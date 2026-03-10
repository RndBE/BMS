<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // SVG canvas assumed 800x520 viewBox
        $rooms = [
            ['name' => 'Lobby',           'code' => 'LBY', 'svg_x' => 20,  'svg_y' => 20,  'svg_width' => 120, 'svg_height' => 100, 'status' => 'normal'],
            ['name' => 'Ruang HRD',       'code' => 'HRD', 'svg_x' => 20,  'svg_y' => 140, 'svg_width' => 120, 'svg_height' => 80,  'status' => 'normal'],
            ['name' => 'Ruang Finance',   'code' => 'FIN', 'svg_x' => 20,  'svg_y' => 240, 'svg_width' => 120, 'svg_height' => 80,  'status' => 'normal'],
            ['name' => 'Ruang Direksi',   'code' => 'DIR', 'svg_x' => 20,  'svg_y' => 340, 'svg_width' => 120, 'svg_height' => 80,  'status' => 'poor'],
            ['name' => 'Toilet',          'code' => 'TLT', 'svg_x' => 20,  'svg_y' => 440, 'svg_width' => 120, 'svg_height' => 60,  'status' => 'normal'],
            ['name' => 'Ruang IT',        'code' => 'IT',  'svg_x' => 180, 'svg_y' => 20,  'svg_width' => 140, 'svg_height' => 90,  'status' => 'normal'],
            ['name' => 'Ruang Software',  'code' => 'SWR', 'svg_x' => 180, 'svg_y' => 130, 'svg_width' => 140, 'svg_height' => 100, 'status' => 'warning'],
            ['name' => 'Ruang Meeting',   'code' => 'MTG', 'svg_x' => 180, 'svg_y' => 250, 'svg_width' => 140, 'svg_height' => 100, 'status' => 'warning'],
            ['name' => 'Ruang Marketing', 'code' => 'MKT', 'svg_x' => 360, 'svg_y' => 20,  'svg_width' => 140, 'svg_height' => 90,  'status' => 'normal'],
            ['name' => 'Ruang Produksi',  'code' => 'PRD', 'svg_x' => 360, 'svg_y' => 130, 'svg_width' => 140, 'svg_height' => 220, 'status' => 'normal'],
            ['name' => 'Ruang Gudang',    'code' => 'GDG', 'svg_x' => 540, 'svg_y' => 20,  'svg_width' => 230, 'svg_height' => 330, 'status' => 'normal'],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
