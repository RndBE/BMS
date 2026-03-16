<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin',      'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user',       'guard_name' => 'web']);

        // Superadmin user
        $user = User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $user->syncRoles([$superadminRole]);

        // BMS data seeders (order matters for FK constraints)
        $this->call([
            RoomSeeder::class,
            SensorGroupSeeder::class,
            SensorSeeder::class,
            SensorParameterSeeder::class,
            SensorReadingSeeder::class,
            AcUnitSeeder::class,
            AlertSeeder::class,
            BuildingSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
