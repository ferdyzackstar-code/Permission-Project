<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class ProductsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private array $failures = [];
    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena index 0-based + heading row

            $name = trim((string) ($row['name'] ?? ''));
            $price = trim((string) ($row['price'] ?? ''));
            $stock = trim((string) ($row['stock'] ?? ''));
            $speciesId = trim((string) ($row['species_id'] ?? ''));
            $categoryId = trim((string) ($row['category_id'] ?? ''));

            // Skip baris kosong sepenuhnya
            if ($name === '' && $price === '' && $stock === '' && $speciesId === '' && $categoryId === '') {
                continue;
            }

            // Validasi manual
            $errors = [];

            if ($name === '') {
                $errors[] = 'Kolom nama produk wajib diisi.';
            } elseif (Product::where('name', $name)->exists()) {
                $errors[] = "Produk \"{$name}\" sudah terdaftar di sistem.";
            }

            if ($price === '') {
                $errors[] = 'Kolom harga wajib diisi.';
            } elseif (!is_numeric($price) || $price < 0) {
                $errors[] = 'Harga harus berupa angka positif.';
            }

            if ($stock === '') {
                $errors[] = 'Kolom stok wajib diisi.';
            } elseif (!ctype_digit($stock)) {
                $errors[] = 'Stok harus berupa bilangan bulat.';
            }

            if ($speciesId === '') {
                $errors[] = 'ID Species wajib diisi.';
            } elseif (!Category::where('id', $speciesId)->exists()) {
                $errors[] = 'ID Species tidak terdaftar di sistem.';
            }

            if ($categoryId === '') {
                $errors[] = 'ID Kategori wajib diisi.';
            } else {
                $category = Category::find($categoryId);
                if (!$category) {
                    $errors[] = 'ID Kategori tidak ditemukan di sistem.';
                } elseif (is_null($category->parent_id)) {
                    $errors[] = 'ID yang dimasukkan adalah ID Species. Gunakan ID Sub-Kategori.';
                }
            }

            if (!empty($errors)) {
                $this->failures[] = "Baris {$rowNumber}: " . implode(', ', $errors);
                continue;
            }

            // Semua valid — simpan ke DB
            Product::create([
                'name' => $name,
                'category_id' => $categoryId,
                'price' => $price,
                'stock' => $stock,
                'detail' => trim((string) ($row['detail'] ?? '')) ?: null,
                'status' => 'active',
            ]);

            $this->importedCount++;
        }
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
