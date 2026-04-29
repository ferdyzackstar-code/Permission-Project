<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ─── CRUD Entities ────────────────────────────────────────────────
        $entities = ['supplier', 'product', 'category', 'role', 'user', 'permission'];
        $actions = ['index', 'show', 'edit', 'delete', 'create'];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => $entity . '.' . $action,
                    'guard_name' => 'web',
                ]);
            }
        }

        // ─── Order ────────────────────────────────────────────────────────
        $orderPermissions = [
            'order.history', // index.blade     → index()
            'order.pos', // pos.blade        → pos(), store()
            'order.confirm', // confirmation.blade → confirmation(), confirmPayment(), approve(), cancel()
            'order.receipt', // receipt.blade    → receipt()
        ];

        foreach ($orderPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // ─── Report ───────────────────────────────────────────────────────
        $reportPermissions = [
            'report.hourly', // hourly.blade & pdf_hourly.blade  → hourlyReport(), exportHourlyPdf()
            'report.daily', // daily.blade & pdf_daily.blade    → dailyReport(), exportDailyPdf()
            'report.monthly', // monthly.blade & pdf_monthly.blade → monthlyReport(), exportMonthlyPdf()
        ];

        foreach ($reportPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // ─── Setting ──────────────────────────────────────────────────────
        Permission::firstOrCreate([
            'name' => 'setting.index', // index.blade → index(), update()
            'guard_name' => 'web',
        ]);

        // ─── Purchase ─────────────────────────────────────────────────────
        $purchasePermissions = [
            'purchase.index', // index.blade        → index(), store(), update(), show()
            'purchase.confirm', // confirmation.blade → confirmation(), approve(), cancel()
        ];

        foreach ($purchasePermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ PermissionSeeder berhasil di-seed!');
    }
}
