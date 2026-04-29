<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Mapping: Cari sub-kategori berdasarkan kombinasi nama sub + parent.
         * Contoh: slug 'makanan-kucing' = subCategory 'Makanan' dengan parent 'Kucing'
         *
         * CategorySeeder membuat slug dengan format: Str::slug($sub . ' ' . $main)
         * Contoh: slug('Makanan Kucing') = 'makanan-kucing'
         */
        $getCategoryId = function (string $mainName, string $subName): int {
            $slug = \Illuminate\Support\Str::slug($subName . ' ' . $mainName);
            $category = Category::where('slug', $slug)->firstOrFail();
            return $category->id;
        };

        $products = [
            // ─── Kucing → Makanan ──────────────────────────────────────────
            [
                'name' => 'Whiskas Tuna Adult 1.2kg',
                'detail' => 'Makanan kucing dewasa rasa tuna dengan kandungan omega-3 untuk kesehatan bulu dan kulit.',
                'image' => '1774846398-whiskas-tuna-adult-12kg.jpg',
                'category_id' => $getCategoryId('Kucing', 'Makanan'),
                'price' => 45000,
                'stock' => 50,
            ],
            [
                'name' => 'Royal Canin Kitten 400g',
                'detail' => 'Nutrisi lengkap khusus anak kucing usia 4–12 bulan untuk pertumbuhan optimal.',
                'image' => '1774846433-royal-canin-kitten-400g.jpg',
                'category_id' => $getCategoryId('Kucing', 'Makanan'),
                'price' => 75000,
                'stock' => 50,
            ],

            // ─── Kucing → Obat ─────────────────────────────────────────────
            [
                'name' => 'Drontal Cat (Obat Cacing)',
                'detail' => 'Obat cacing spektrum luas untuk kucing, efektif melawan cacing gelang, cacing pita, dan cacing tambang.',
                'image' => '1775704927-drontal-cat-obat-cacing.jpg',
                'category_id' => $getCategoryId('Kucing', 'Obat'),
                'price' => 35000,
                'stock' => 50,
            ],
            [
                'name' => 'Revolution Cat Anti Kutu & Tungau',
                'detail' => 'Obat tetes anti parasit untuk kucing, melindungi dari kutu, tungau telinga, dan cacing jantung.',
                'image' => 'default-product.jpg',
                'category_id' => $getCategoryId('Kucing', 'Obat'),
                'price' => 95000,
                'stock' => 50,
            ],

            // ─── Kucing → Kandang ──────────────────────────────────────────
            [
                'name' => 'Kandang Kucing Lipat Besi Tingkat Size M',
                'detail' => 'Kandang besi kokoh dua lantai ukuran 50x35x75 cm, mudah dilipat dan dibawa.',
                'image' => 'default-product.jpg',
                'category_id' => $getCategoryId('Kucing', 'Kandang'),
                'price' => 185000,
                'stock' => 50,
            ],

            // ─── Anjing → Makanan ──────────────────────────────────────────
            [
                'name' => 'Pedigree Beef Puppy 1.5kg',
                'detail' => 'Makanan anjing puppy rasa sapi dengan DHA untuk perkembangan otak dan tulang.',
                'image' => '1774846527-pedigree-beef-puppy-15kg.jpg',
                'category_id' => $getCategoryId('Anjing', 'Makanan'),
                'price' => 55000,
                'stock' => 50,
            ],
            [
                'name' => 'Royal Canin Medium Adult 4kg',
                'detail' => 'Makanan anjing dewasa ras sedang dengan formula khusus untuk menjaga kesehatan pencernaan.',
                'image' => 'default-product.jpg',
                'category_id' => $getCategoryId('Anjing', 'Makanan'),
                'price' => 235000,
                'stock' => 50,
            ],

            // ─── Anjing → Obat ─────────────────────────────────────────────
            [
                'name' => 'Drontal Dog Obat Cacing Anjing',
                'detail' => 'Obat cacing komprehensif untuk anjing dewasa, aman dan efektif untuk berbagai jenis cacing.',
                'image' => 'default-product.jpg',
                'category_id' => $getCategoryId('Anjing', 'Obat'),
                'price' => 38000,
                'stock' => 50,
            ],

            // ─── Anjing → Kandang ──────────────────────────────────────────
            [
                'name' => 'Kandang Besi Lipat Tingkat Size L',
                'detail' => 'Kandang besi kokoh ukuran 60x40x50 cm, ideal untuk anjing ras kecil hingga sedang.',
                'image' => '1774846583-kandang-besi-lipat-tingkat-size-l.jpg',
                'category_id' => $getCategoryId('Anjing', 'Kandang'),
                'price' => 225000,
                'stock' => 50,
            ],

            // ─── Burung → Makanan ──────────────────────────────────────────
            [
                'name' => 'Pakan Burung Gold Coin 250g',
                'detail' => 'Pakan harian bernutrisi tinggi untuk burung kicau, campuran biji-bijian pilihan premium.',
                'image' => '1777360301-pakan-burung-gold-coin-250g.jpg',
                'category_id' => $getCategoryId('Burung', 'Makanan'),
                'price' => 22000,
                'stock' => 50,
            ],
            [
                'name' => 'Voer Burung Merk Doa Ibu 500g',
                'detail' => 'Voer lengkap untuk burung kicau dengan kandungan protein dan vitamin seimbang.',
                'image' => 'default-product.jpg',
                'category_id' => $getCategoryId('Burung', 'Makanan'),
                'price' => 18000,
                'stock' => 50,
            ],

            // ─── Burung → Obat ─────────────────────────────────────────────
            [
                'name' => 'BirdVit Vitamin Burung 30ml',
                'detail' => 'Suplemen vitamin lengkap untuk burung, meningkatkan daya tahan tubuh dan kebugaran.',
                'image' => 'default-product.jpg',
                'category_id' => $getCategoryId('Burung', 'Obat'),
                'price' => 28000,
                'stock' => 50,
            ],

            // ─── Burung → Kandang ──────────────────────────────────────────
            [
                'name' => 'Sangkar Burung Bulat Rotan Natural',
                'detail' => 'Sangkar burung berbahan rotan alami finishing natural, ukuran diameter 35 cm, cocok untuk kenari dan lovebird.',
                'image' => 'default-product.jpg',
                'category_id' => $getCategoryId('Burung', 'Kandang'),
                'price' => 85000,
                'stock' => 50,
            ],
        ];

        foreach ($products as $item) {
            Product::create([
                'name' => $item['name'],
                'detail' => $item['detail'],
                'category_id' => $item['category_id'],
                'price' => $item['price'],
                'stock' => $item['stock'],
                'image' => $item['image'],
                'status' => 'active',
            ]);
        }

        $this->command->info('✅ ProductSeeder berhasil di-seed! Total: ' . count($products) . ' produk.');
    }
}
