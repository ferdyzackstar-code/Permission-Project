<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Definisi permission per grup ────────────────────────────────

        $orderPermissions = ['order.history', 'order.pos', 'order.confirm', 'order.receipt'];

        // Semua permission CRUD entitas standar
        $entities = ['supplier', 'product', 'category', 'role', 'user', 'permission'];
        $actions = ['index', 'show', 'edit', 'delete', 'create'];

        $crudPermissions = [];
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $crudPermissions[] = $entity . '.' . $action;
            }
        }

        $reportPermissions = ['report.hourly', 'report.daily', 'report.monthly'];

        $settingPermissions = ['setting.index'];

        $purchasePermissions = ['purchase.index', 'purchase.confirm'];

        // ─── Role: Admin ──────────────────────────────────────────────────
        // Semua permission KECUALI order.*
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        $adminPermissions = array_merge($crudPermissions, $reportPermissions, $settingPermissions, $purchasePermissions);

        $adminRole->syncPermissions(Permission::whereIn('name', $adminPermissions)->get());

        // ─── Role: Kasir ──────────────────────────────────────────────────
        // Hanya order.* saja
        $kasirRole = Role::firstOrCreate(['name' => 'Kasir']);

        $kasirRole->syncPermissions(Permission::whereIn('name', $orderPermissions)->get());

        // ─── Role: User ───────────────────────────────────────────────────
        // Read-only: .index dan .show dari semua entitas + report + purchase
        $readOnlyActions = ['index', 'show'];
        $readOnlyEntities = ['supplier', 'product', 'category', 'role', 'user', 'permission'];

        $userPermissions = [];

        // CRUD entitas → read-only
        foreach ($readOnlyEntities as $entity) {
            foreach ($readOnlyActions as $action) {
                $userPermissions[] = $entity . '.' . $action;
            }
        }

        // Report → read-only (bisa melihat laporan, tidak bisa ekspor/modifikasi)
        $userPermissions[] = 'report.hourly';
        $userPermissions[] = 'report.daily';
        $userPermissions[] = 'report.monthly';

        // Purchase → read-only
        $userPermissions[] = 'purchase.index';

        // Setting → read-only (bisa melihat halaman setting)
        $userPermissions[] = 'setting.index';

        $userRole = Role::firstOrCreate(['name' => 'User']);

        $userRole->syncPermissions(Permission::whereIn('name', $userPermissions)->get());

        $this->command->info('✅ RoleSeeder berhasil di-seed!');
        $this->command->table(['Role', 'Jumlah Permission'], [['Admin', count($adminPermissions)], ['Kasir', count($orderPermissions)], ['User', count($userPermissions)]]);
    }
}
