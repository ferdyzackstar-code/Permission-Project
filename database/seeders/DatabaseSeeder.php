<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SettingAppSeeder::class, // 2. Setting aplikasi (tidak ada dependency)
            PermissionSeeder::class, // 3. Permission harus ada sebelum Role
            RoleSeeder::class, // 4. Role assign permission → butuh Permission
            UserSeeder::class, // 5. User assign role → butuh Role
            CategorySeeder::class, // 6. Category (parent dulu, lalu sub)
            SupplierSeeder::class, // 7. Supplier (tidak ada dependency)
            ProductSeeder::class, // 8. Product → butuh Category (category_id)
        ]);
    }
}
