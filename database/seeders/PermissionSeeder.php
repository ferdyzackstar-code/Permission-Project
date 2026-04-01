<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $entities = ['supplier', 'product', 'category', 'role', 'user', 'outlet', 'permission'];
        $actions = ['index', 'show', 'edit', 'delete'];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => $entity . '.' . $action,
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}