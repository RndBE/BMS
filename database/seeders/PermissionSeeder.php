<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define all permissions ─────────────────────────────────────────────
        $permissions = [
            // Denah (floor plan editor)
            'kelola_denah',

            // Konfigurasi
            'kelola_konfigurasi',

            // Peringatan
            'kelola_peringatan',

            // Pengguna
            'kelola_pengguna',

            // Pengaturan umum
            'kelola_pengaturan',

            // Log
            'lihat_log',

            // Analisa data
            'lihat_analisa',

            // Energi
            'lihat_energi',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Assign permissions to roles ────────────────────────────────────────

        // superadmin — semua akses
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadmin->syncPermissions($permissions);

        // admin — semua kecuali kelola_denah & kelola_pengguna
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'kelola_konfigurasi',
            'kelola_peringatan',
            'kelola_pengaturan',
            'lihat_log',
            'lihat_analisa',
            'lihat_energi',
        ]);

        // user — hanya lihat
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->syncPermissions([
            'lihat_log',
            'lihat_analisa',
            'lihat_energi',
        ]);
    }
}
