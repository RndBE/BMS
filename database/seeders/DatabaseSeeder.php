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
        // 1. Permissions & Roles MUST run first
        $this->call(PermissionSeeder::class);

        // 2. Superadmin user
        $superadminRole = Role::where('name', 'superadmin')->first();
        $user = User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if ($superadminRole) {
            $user->syncRoles([$superadminRole]);
        }

        // 3. BMS data seeders (order matters for FK constraints)
        $this->call([
            BuildingSeeder::class,       // buildings first (floors depend on it)
            RoomSeeder::class,           // rooms depend on floors
            SensorGroupSeeder::class,
            SensorSeeder::class,
            SensorParameterSeeder::class,
            SensorReadingSeeder::class,
            AcUnitSeeder::class,
            AlertSeeder::class,
            AlertLimitSeeder::class,     // batas normal peringatan
            AlertRuleSeeder::class,      // aturan peringatan
            SettingSeeder::class,
        ]);
    }
}
