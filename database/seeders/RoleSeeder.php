<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menghapus cache permission agar role baru langsung terdeteksi
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Membuat Role Admin
        Role::firstOrCreate(['name' => 'admin']);

        // Membuat Role Staff/User (Opsional, tambah sesuai kebutuhan)
        Role::firstOrCreate(['name' => 'Quality Control']);
        Role::firstOrCreate(['name' => 'General Manager']);
        Role::firstOrCreate(['name' => 'Factory Manager']);
        Role::firstOrCreate(['name' => 'QC Manager']);
        Role::firstOrCreate(['name' => 'Manager']);
        Role::firstOrCreate(['name' => 'Supervisor']);
        Role::firstOrCreate(['name' => 'Leader']);
        Role::firstOrCreate(['name' => 'Operator']);

    }
}
