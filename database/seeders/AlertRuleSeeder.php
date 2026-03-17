<?php

namespace Database\Seeders;

use App\Models\AlertRule;
use Illuminate\Database\Seeder;

class AlertRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name'                 => 'Suhu Ruang Terlalu Tinggi',
                'kategori'             => 'Kenyamanan',
                'parameter_key'        => 'suhu',
                'condition'            => '>',
                'threshold'            => 28,
                'severity'             => 'warning',
                'notification_channel' => 'whatsapp',
                'durasi_tunda'         => 5,
                'room_ids'             => [],
                'is_active'            => true,
            ],
            [
                'name'                 => 'Suhu Ruang Terlalu Rendah',
                'kategori'             => 'Kenyamanan',
                'parameter_key'        => 'suhu',
                'condition'            => '<',
                'threshold'            => 18,
                'severity'             => 'warning',
                'notification_channel' => 'whatsapp',
                'durasi_tunda'         => 5,
                'room_ids'             => [],
                'is_active'            => true,
            ],
            [
                'name'                 => 'Kelembaban Rendah',
                'kategori'             => 'Kenyamanan',
                'parameter_key'        => 'kelembaban',
                'condition'            => '<',
                'threshold'            => 30,
                'severity'             => 'warning',
                'notification_channel' => null,
                'durasi_tunda'         => 10,
                'room_ids'             => [],
                'is_active'            => true,
            ],
            [
                'name'                 => 'CO₂ Tinggi',
                'kategori'             => 'Kenyamanan',
                'parameter_key'        => 'co2',
                'condition'            => '>',
                'threshold'            => 1000,
                'severity'             => 'critical',
                'notification_channel' => 'whatsapp',
                'durasi_tunda'         => 15,
                'room_ids'             => [],
                'is_active'            => true,
            ],
            [
                'name'                 => 'Daya Berlebih',
                'kategori'             => 'Efisiensi',
                'parameter_key'        => 'daya',
                'condition'            => '>',
                'threshold'            => 7,
                'severity'             => 'warning',
                'notification_channel' => null,
                'durasi_tunda'         => 15,
                'room_ids'             => [],
                'is_active'            => true,
            ],
        ];

        foreach ($rules as $rule) {
            AlertRule::updateOrCreate(
                ['name' => $rule['name'], 'parameter_key' => $rule['parameter_key']],
                $rule
            );
        }
    }
}
