<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name'        => 'Beacon Engineering',
            'timezone'         => 'Asia/Jakarta',
            'date_format'      => 'DD/MM/YYYY',
            'time_format'      => '24',
            'refresh_interval' => '86400',
            'default_range'    => 'harian',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
