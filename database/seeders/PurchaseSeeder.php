<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\Product;
use Carbon\Carbon;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();

        if ($suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->error('Supplier atau Product belum ada. Jalankan SupplierSeeder & ProductSeeder terlebih dahulu.');
            return;
        }

        // ── Spread waktu: 3 bulan terakhir, bolong-bolong ───────────────
        $slots = $this->generatePurchaseSlots(now()->subMonths(3), now(), 13);

        $purchases = [
            // ── Status: received (mayoritas) ────────────────────────────
            [
                'status' => 'received',
                'items_count' => 3,
                'notes' => 'Stok rutin bulanan. Semua barang diterima dalam kondisi baik.',
            ],
            [
                'status' => 'received',
                'items_count' => 2,
                'notes' => 'Restock produk makanan kucing dan anjing.',
            ],
            [
                'status' => 'received',
                'items_count' => 4,
                'notes' => 'Pembelian batch pertama bulan ini. Kondisi oke.',
            ],
            [
                'status' => 'received',
                'items_count' => 2,
                'notes' => 'Tambahan stok obat-obatan hewan.',
            ],
            [
                'status' => 'received',
                'items_count' => 3,
                'notes' => 'Restock mingguan. Semua item sesuai PO.',
            ],
            [
                'status' => 'received',
                'items_count' => 2,
                'notes' => 'Pembelian rutin. Stok kandang dan aksesoris.',
            ],
            [
                'status' => 'received',
                'items_count' => 3,
                'notes' => 'Restock produk premium. Diterima lengkap.',
            ],
            [
                'status' => 'received',
                'items_count' => 2,
                'notes' => null,
            ],

            // ── Status: pending ──────────────────────────────────────────
            [
                'status' => 'pending',
                'items_count' => 2,
                'notes' => 'Menunggu konfirmasi pengiriman dari supplier.',
            ],
            [
                'status' => 'pending',
                'items_count' => 3,
                'notes' => 'PO sudah dikirim, menunggu respons supplier.',
            ],
            [
                'status' => 'pending',
                'items_count' => 2,
                'notes' => null,
            ],

            // ── Status: cancelled ────────────────────────────────────────
            [
                'status' => 'cancelled',
                'items_count' => 2,
                'notes' => 'Dibatalkan karena stok masih mencukupi.',
            ],
            [
                'status' => 'cancelled',
                'items_count' => 1,
                'notes' => 'Supplier tidak dapat memenuhi pesanan, order dibatalkan.',
            ],
        ];

        $purchaseCounter = 1;

        foreach ($purchases as $index => $template) {
            $timestamp = $slots[$index] ?? now()->subDays(rand(1, 90));
            $supplier = $suppliers->random();
            $purchaseNum = 'PO-' . $timestamp->format('Ymd') . '-' . str_pad($purchaseCounter++, 4, '0', STR_PAD_LEFT);

            // ── Pilih produk random tanpa duplikat per purchase ──────────
            $selectedProducts = $products->random(min($template['items_count'], $products->count()));
            $totalAmount = 0;
            $itemsPayload = [];

            foreach ($selectedProducts as $product) {
                $qty = rand(5, 20); // Pembelian ke supplier lebih banyak dari penjualan

                // Harga beli dari supplier = 60–75% dari harga jual (margin realistis)
                $buyPrice = round($product->price * (rand(60, 75) / 100), -2);
                $subtotal = $qty * $buyPrice;
                $totalAmount += $subtotal;

                $itemsPayload[] = [
                    'purchase_id' => null, // diisi setelah insert purchase
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $buyPrice,
                    'subtotal' => $subtotal,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            // ── Insert Purchase ──────────────────────────────────────────
            $purchaseId = DB::table('purchases')->insertGetId([
                'supplier_id' => $supplier->id,
                'purchase_date' => $timestamp->toDateString(),
                'purchase_number' => $purchaseNum,
                'total_amount' => $totalAmount,
                'notes' => $template['notes'],
                'status' => $template['status'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            // ── Insert Purchase Items ────────────────────────────────────
            foreach ($itemsPayload as &$item) {
                $item['purchase_id'] = $purchaseId;
            }
            DB::table('purchase_items')->insert($itemsPayload);
        }

        $this->command->info('✅ PurchaseSeeder berhasil di-seed! Total: ' . count($purchases) . ' purchase.');
        $this->command->table(['Status', 'Jumlah'], [['received', collect($purchases)->where('status', 'received')->count()], ['pending', collect($purchases)->where('status', 'pending')->count()], ['cancelled', collect($purchases)->where('status', 'cancelled')->count()]]);
    }

    /**
     * Generate timestamp slots untuk purchase.
     * Purchase biasanya terjadi di jam kerja: 08:00 - 16:00
     * dan tidak setiap hari (supplier visit biasanya mingguan).
     */
    private function generatePurchaseSlots(Carbon $start, Carbon $end, int $count): array
    {
        // Jam kerja untuk pembelian/supplier
        $workHours = [8, 9, 10, 11, 13, 14, 15];

        $totalDays = $start->diffInDays($end);
        $slots = [];

        // Purchase lebih jarang — sekitar 1x per minggu, bolong-bolong
        // Pilih hari secara acak, skip banyak hari
        $activeDays = collect(range(0, $totalDays))
            ->filter(fn($d) => rand(1, 7) === 1) // ~1 dari 7 hari ada purchase
            ->shuffle()
            ->take($count + 5)
            ->sort()
            ->values();

        $i = 0;
        foreach ($activeDays as $dayOffset) {
            if ($i >= $count) {
                break;
            }

            $date = $start->copy()->addDays($dayOffset);
            $hour = $workHours[array_rand($workHours)];

            $slots[] = $date->copy()->setHour($hour)->setMinute(rand(0, 59))->setSecond(rand(0, 59));

            $i++;
        }

        // Jika slot kurang dari count, tambahkan secara manual
        while (count($slots) < $count) {
            $slots[] = $start
                ->copy()
                ->addDays(rand(0, $totalDays))
                ->setHour($workHours[array_rand($workHours)])
                ->setMinute(rand(0, 59));
        }

        usort($slots, fn($a, $b) => $a->timestamp <=> $b->timestamp);

        return array_slice($slots, 0, $count);
    }
}
