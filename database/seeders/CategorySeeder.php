<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $mainCategories = ['Kucing', 'Anjing', 'Burung'];
        $subCategories = ['Makanan', 'Obat', 'Kandang'];

        foreach ($mainCategories as $main) {

            $parent = Category::create([
                'name' => $main,
                'parent_id' => null,
                'slug' => Str::slug($main),
                'description' => 'Kategori utama untuk ' . $main,
                'status' => 'active', 
            ]);

            foreach ($subCategories as $sub) {
                Category::create([
                    'name' => $sub,
                    'parent_id' => $parent->id,
                    'slug' => Str::slug($sub . ' ' . $main),
                    'description' => 'Kategori ' . $sub . ' untuk ' . $main,
                    'status' => 'active',
                ]);
            }
        }
    }
}
