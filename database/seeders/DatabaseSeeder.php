<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin user
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // BMS data seeders (order matters for FK constraints)
        $this->call([
            RoomSeeder::class,
            SensorSeeder::class,
            SensorReadingSeeder::class,
            AcUnitSeeder::class,
            AlertSeeder::class,
        ]);
    }
}
