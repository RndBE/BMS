<?php

namespace Database\Seeders;

use App\Models\AlertLimit;
use Illuminate\Database\Seeder;

class AlertLimitSeeder extends Seeder
{
    public function run(): void
    {
        $limits = [
            [
                'parameter_key' => 'suhu',
                'label'         => 'Suhu (°C)',
                'icon'          => 'icons/suhu.svg',
                'icon_type'     => 'img',
                'normal_min'    => 23,
                'normal_max'    => 26,
                'warn_low_min'  => 21,
                'warn_low_max'  => 23,
                'warn_high_min' => 26,
                'warn_high_max' => 28,
                'poor_low'      => 21,
                'poor_high'     => 28,
            ],
            [
                'parameter_key' => 'kelembaban',
                'label'         => 'Kelembaban (%)',
                'icon'          => 'icons/kelembapan.svg',
                'icon_type'     => 'img',
                'normal_min'    => 40,
                'normal_max'    => 60,
                'warn_low_min'  => 30,
                'warn_low_max'  => 40,
                'warn_high_min' => 60,
                'warn_high_max' => 70,
                'poor_low'      => 30,
                'poor_high'     => 70,
            ],
            [
                'parameter_key' => 'co2',
                'label'         => 'CO₂ (ppm)',
                'icon'          => 'icons/co2.svg',
                'icon_type'     => 'img',
                'normal_min'    => null,
                'normal_max'    => 800,
                'warn_low_min'  => null,
                'warn_low_max'  => null,
                'warn_high_min' => null,
                'warn_high_max' => null,
                'poor_low'      => null,
                'poor_high'     => 1000,
            ],
            [
                'parameter_key' => 'daya',
                'label'         => 'Daya (kW)',
                'icon'          => 'icons/daya.svg',
                'icon_type'     => 'img',
                'normal_min'    => null,
                'normal_max'    => 5,
                'warn_low_min'  => null,
                'warn_low_max'  => null,
                'warn_high_min' => null,
                'warn_high_max' => null,
                'poor_low'      => null,
                'poor_high'     => 7,
            ],
            [
                'parameter_key' => 'tegangan',
                'label'         => 'Tegangan (Volt)',
                'icon'          => 'icons/tegangan.svg',
                'icon_type'     => 'img',
                'normal_min'    => null,
                'normal_max'    => 240,
                'warn_low_min'  => null,
                'warn_low_max'  => null,
                'warn_high_min' => null,
                'warn_high_max' => null,
                'poor_low'      => null,
                'poor_high'     => 250,
            ],
        ];

        foreach ($limits as $limit) {
            AlertLimit::updateOrCreate(
                ['parameter_key' => $limit['parameter_key']],
                $limit
            );
        }
    }
}
