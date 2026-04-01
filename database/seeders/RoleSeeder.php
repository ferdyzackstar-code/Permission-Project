<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);

        // 2. Ambil semua permission yang baru saja dibuat di PermissionSeeder
        $allPermissions = Permission::all();

        // 3. Admin dikasih SEMUA AKSES
        $adminRole->givePermissionTo($allPermissions);
    }
}