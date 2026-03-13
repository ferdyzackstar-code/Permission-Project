<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $outlets = Outlet::all();
        $users = User::all();
        $products = Product::all();

        if ($outlets->isEmpty() || $users->isEmpty() || $products->isEmpty()) {
            $this->command->error('Pastikan data Outlet, User, dan Product sudah ada!');
            return;
        }

        foreach ($outlets as $outlet) {
            // Per outlet kita buat 15 transaksi
            for ($i = 1; $i <= 15; $i++) {
                DB::transaction(function () use ($outlet, $users, $products) {
                    $randomDate = now()->subDays(rand(0, 30))->subMinutes(rand(1, 1440));
                    $kasir = $users->random();

                    // 1. Buat Header Transaksi dulu (Total masih 0)
                    $transaction = Transaction::create([
                        'invoice_number' => 'INV-' . $randomDate->format('Ymd') . '-' . strtoupper(Str::random(5)),
                        'outlet_id' => $outlet->id,
                        'user_id' => $kasir->id,
                        'total_price' => 0, // Akan diupdate nanti
                        'paid_amount' => 0,
                        'change_amount' => 0,
                        'created_at' => $randomDate,
                    ]);

                    $runningTotal = 0;
                    // 2. Pilih 1 sampai 4 produk acak untuk transaksi ini
                    $randomProducts = $products->random(rand(1, 4));

                    foreach ($randomProducts as $product) {
                        $qty = rand(1, 3);
                        $subtotal = $product->price * $qty;

                        // Tambahkan ke Detail
                        TransactionDetail::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $product->price,
                            'subtotal' => $subtotal,
                        ]);

                        // Kurangi Stok Produk (Logika Inventory Dasar)
                        $product->decrement('stock', $qty);

                        $runningTotal += $subtotal;
                    }

                    // 3. Update Total Harga di Header
                    $paid = ceil($runningTotal / 50000) * 50000; // Simulasi uang bayar (kelipatan 50rb)
                    $transaction->update([
                        'total_price' => $runningTotal,
                        'paid_amount' => $paid,
                        'change_amount' => $paid - $runningTotal,
                    ]);
                });
            }
        }

        $this->command->info('Seeding berhasil: Transaksi, Detail, dan Pengurangan Stok selesai!');
    }
}
