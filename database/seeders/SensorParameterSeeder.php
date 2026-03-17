<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\SensorParameter;
use Illuminate\Database\Seeder;

class SensorParameterSeeder extends Seeder
{
    /**
     * Parameter default per ruangan.
     * Setiap ruangan mendapat set parameter yang sama (sensor1–sensor5 untuk 5 parameter utama).
     * 'kolom_reading' = kolom di tabel sensor_readings yang menyimpan nilai parameter ini.
     */
    private array $parameters = [
        ['nama_parameter' => 'Suhu',           'unit' => '°C',    'kolom_reading' => 'sensor1',  'sort_order' => 1],
        ['nama_parameter' => 'Kelembaban',      'unit' => '%',     'kolom_reading' => 'sensor2',  'sort_order' => 2],
        ['nama_parameter' => 'Tegangan',        'unit' => 'V',     'kolom_reading' => 'sensor3',  'sort_order' => 3],
        ['nama_parameter' => 'Daya',            'unit' => 'W',     'kolom_reading' => 'sensor4',  'sort_order' => 4],
        ['nama_parameter' => 'CO₂',             'unit' => 'ppm',   'kolom_reading' => 'sensor5',  'sort_order' => 5],
        ['nama_parameter' => 'Tekanan',         'unit' => 'hPa',   'kolom_reading' => 'sensor6',  'sort_order' => 6],
        ['nama_parameter' => 'Cahaya',          'unit' => 'lux',   'kolom_reading' => 'sensor7',  'sort_order' => 7],
        ['nama_parameter' => 'Kecepatan Angin', 'unit' => 'm/s',   'kolom_reading' => 'sensor8',  'sort_order' => 8],
        ['nama_parameter' => 'PM2.5',           'unit' => 'µg/m³', 'kolom_reading' => 'sensor9',  'sort_order' => 9],
        ['nama_parameter' => 'PM10',            'unit' => 'µg/m³', 'kolom_reading' => 'sensor10', 'sort_order' => 10],
        ['nama_parameter' => 'VOC',             'unit' => 'ppb',   'kolom_reading' => 'sensor11', 'sort_order' => 11],
        ['nama_parameter' => 'Noise',           'unit' => 'dB',    'kolom_reading' => 'sensor12', 'sort_order' => 12],
        ['nama_parameter' => 'Frekuensi',       'unit' => 'Hz',    'kolom_reading' => 'sensor13', 'sort_order' => 13],
        ['nama_parameter' => 'Power Factor',    'unit' => '-',     'kolom_reading' => 'sensor14', 'sort_order' => 14],
    ];

    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            foreach ($this->parameters as $param) {
                SensorParameter::updateOrCreate(
                    [
                        'room_id'       => $room->id,
                        'kolom_reading' => $param['kolom_reading'],
                    ],
                    [
                        'nama_parameter' => $param['nama_parameter'],
                        'unit'           => $param['unit'],
                        'sort_order'     => $param['sort_order'],
                    ]
                );
            }
        }
    }
}
