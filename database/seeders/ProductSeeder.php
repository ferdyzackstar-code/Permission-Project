<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $subCategories = Category::whereNotNull('parent_id')->get();

        $products = [
            [
                'name' => 'Whiskas Tuna Adult 1.2kg', 
                'detail' => 'Makanan kucing dewasa rasa tuna.',
                'image' => '1774846398-whiskas-tuna-adult-12kg.jpg'
            ],
            [
                'name' => 'Royal Canin Kitten 400g', 
                'detail' => 'Nutrisi khusus anak kucing.',
                'image' => '1774846433-royal-canin-kitten-400g.jpg'
            ],
            [
                'name' => 'Pedigree Beef Puppy 1.5kg', 
                'detail' => 'Makanan anjing rasa sapi.',
                'image' => '1774846527-pedigree-beef-puppy-15kg.jpg'
            ],
            [
                'name' => 'Drontal Cat (Obat Cacing)', 
                'detail' => 'Obat cacing spektrum luas untuk kucing.',
                'image' => '1775704927-drontal-cat-obat-cacing.jpg'
            ],
            [
                'name' => 'Kandang Besi Lipat Tingkat Size L', 
                'detail' => 'Kandang besi kokoh ukuran 60x40x50 cm.',
                'image' => '1774846583-kandang-besi-lipat-tingkat-size-l.jpg'
            ],
            [
                'name' => 'Pakan Burung Gold Coin 250g', 
                'detail' => 'Pakan harian bernutrisi untuk burung kicau.',
                'image' => '1774846555-pakan-burung-gold-coin-250g.jpg'
            ],
        ];

        foreach ($products as $item) {
            Product::create([
                'name' => $item['name'],
                'detail' => $item['detail'],
                'category_id' => $subCategories->random()->id,
                'price' => rand(20000, 100000), 
                'stock' => rand(10, 100),
                'image' => $item['image'] ?? 'default-product.jpg',
                'status' => 'active', 
            ]);
        }
    }
}