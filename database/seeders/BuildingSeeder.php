<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        // Building 1: Kantor Pusat
        $building = Building::create([
            'name'        => 'Kantor Pusat',
            'code'        => 'KP-01',
            'description' => 'Gedung kantor utama',
            'address'     => 'Jl. Contoh No. 1',
        ]);

        // Floor 1: Lantai 1
        $floor = Floor::create([
            'building_id'  => $building->id,
            'name'         => 'Lantai 1',
            'floor_number' => 1,
            'plan_width'   => 900,
            'plan_height'  => 560,
        ]);

        // Update all existing rooms to belong to this floor
        // and set default marker positions (matching current SVG layout)
        $markerPositions = [
            'HRD' => ['x' => 5.5,  'y' => 17.5],
            'FIN' => ['x' => 5.0,  'y' => 35.0],
            'DIR' => ['x' => 4.2,  'y' => 52.0],
            'TLT' => ['x' => 3.8,  'y' => 67.0],
            'LBY' => ['x' => 31.0, 'y' => 15.0],
            'IT'  => ['x' => 44.4, 'y' => 15.0],
            'SWR' => ['x' => 56.0, 'y' => 15.0],
            'MKT' => ['x' => 68.0, 'y' => 15.0],
            'MTG' => ['x' => 38.0, 'y' => 55.0],
            'PRD' => ['x' => 60.0, 'y' => 55.0],
            'GDG' => ['x' => 84.0, 'y' => 35.0],
        ];

        Room::all()->each(function ($room) use ($floor, $markerPositions) {
            $pos = $markerPositions[$room->code] ?? ['x' => 50, 'y' => 50];
            $room->update([
                'floor_id' => $floor->id,
                'marker_x' => $pos['x'],
                'marker_y' => $pos['y'],
            ]);
        });
    }
}
