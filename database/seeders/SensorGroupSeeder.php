<?php

namespace Database\Seeders;

use App\Models\SensorGroup;
use Illuminate\Database\Seeder;

class SensorGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'kode_sensor' => 'SG-TEMP',
                'nama_sensor' => 'Sensor Suhu',
                'deskripsi'   => 'Mengukur suhu udara ruangan dalam satuan Celsius (°C).',
                'sort_order'  => 1,
            ],
            [
                'kode_sensor' => 'SG-HUM',
                'nama_sensor' => 'Sensor Kelembaban',
                'deskripsi'   => 'Mengukur kelembaban udara relatif dalam satuan persen (%).',
                'sort_order'  => 2,
            ],
            [
                'kode_sensor' => 'SG-ENER',
                'nama_sensor' => 'Sensor Energi',
                'deskripsi'   => 'Mengukur konsumsi energi listrik dalam satuan kWh.',
                'sort_order'  => 3,
            ],
            [
                'kode_sensor' => 'SG-PWR',
                'nama_sensor' => 'Sensor Daya',
                'deskripsi'   => 'Mengukur daya listrik sesaat dalam satuan Watt (W).',
                'sort_order'  => 4,
            ],
        ];

        foreach ($groups as $group) {
            SensorGroup::updateOrCreate(
                ['kode_sensor' => $group['kode_sensor']],
                $group
            );
        }
    }
}
