<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $kasirs = User::role('Kasir')->get();
        $admin = User::role('Admin')->first();
        $products = Product::where('status', 'active')->get();

        if ($kasirs->isEmpty() || $products->isEmpty()) {
            $this->command->error('Kasir atau Product belum ada. Jalankan UserSeeder & ProductSeeder terlebih dahulu.');
            return;
        }

        // ── Spread waktu: 3 bulan terakhir, hari & jam bolong-bolong ──
        $slots = $this->generateTimeSlots(now()->subMonths(3), now(), 35);

        $orders = [
            // ── Status: completed (mayoritas) ───────────────────────────
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'transfer', 'items_count' => 3],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'transfer', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'transfer', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 3],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'transfer', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'transfer', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 3],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'transfer', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'transfer', 'items_count' => 3],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],
            ['status' => 'completed', 'method' => 'transfer', 'items_count' => 1],
            ['status' => 'completed', 'method' => 'cash', 'items_count' => 2],

            // ── Status: pending ──────────────────────────────────────────
            ['status' => 'pending', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'pending', 'method' => 'transfer', 'items_count' => 2],
            ['status' => 'pending', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'pending', 'method' => 'transfer', 'items_count' => 2],
            ['status' => 'pending', 'method' => 'cash', 'items_count' => 1],

            // ── Status: cancelled ────────────────────────────────────────
            ['status' => 'cancelled', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'cancelled', 'method' => 'transfer', 'items_count' => 2],
            ['status' => 'cancelled', 'method' => 'cash', 'items_count' => 1],
            ['status' => 'cancelled', 'method' => 'transfer', 'items_count' => 1],
            ['status' => 'cancelled', 'method' => 'cash', 'items_count' => 2],
        ];

        $invoiceCounter = 1;

        foreach ($orders as $index => $template) {
            $timestamp = $slots[$index] ?? now()->subDays(rand(1, 90));
            $kasir = $kasirs->random();
            $invoiceNum = 'INV-' . $timestamp->format('Ymd') . '-' . str_pad($invoiceCounter++, 4, '0', STR_PAD_LEFT);

            // ── Pilih produk secara random tanpa duplikat per order ──────
            $selectedProducts = $products->random(min($template['items_count'], $products->count()));
            $totalAmount = 0;
            $itemsPayload = [];

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 3);
                $price = $product->price;
                $subtotal = $qty * $price;
                $totalAmount += $subtotal;

                $itemsPayload[] = [
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            // ── Insert Order ─────────────────────────────────────────────
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $kasir->id,
                'invoice_number' => $invoiceNum,
                'total_amount' => $totalAmount,
                'status' => $template['status'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            // ── Insert Order Items ───────────────────────────────────────
            foreach ($itemsPayload as &$item) {
                $item['order_id'] = $orderId;
            }
            DB::table('order_items')->insert($itemsPayload);

            // ── Insert Payment ───────────────────────────────────────────
            $paymentStatus = match ($template['status']) {
                'completed' => 'paid',
                'cancelled' => 'failed',
                default => 'pending',
            };

            // Untuk completed: hitung paid_amount (selalu bulat ke atas ribuan) & kembalian
            $paidAmount = null;
            $changeAmount = null;
            $approvedBy = null;
            $approvedAt = null;

            if ($template['status'] === 'completed') {
                // Bulatkan ke atas (misal total 47.500 → bayar 50.000)
                $paidAmount = ceil($totalAmount / 5000) * 5000;
                $changeAmount = $paidAmount - $totalAmount;
                $approvedBy = $admin->id;
                $approvedAt = $timestamp->copy()->addMinutes(rand(1, 10));
            }

            DB::table('payments')->insert([
                'order_id' => $orderId,
                'payment_method' => $template['method'],
                'payment_status' => $paymentStatus,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        $this->command->info('✅ OrderSeeder berhasil di-seed! Total: ' . count($orders) . ' order.');
        $this->command->table(['Status', 'Jumlah'], [['completed', collect($orders)->where('status', 'completed')->count()], ['pending', collect($orders)->where('status', 'pending')->count()], ['cancelled', collect($orders)->where('status', 'cancelled')->count()]]);
    }

    /**
     * Generate N timestamp slots yang tersebar acak (bolong-bolong)
     * dalam rentang $start sampai $end.
     */
    private function generateTimeSlots(Carbon $start, Carbon $end, int $count): array
    {
        // Jam operasional toko: 09:00 - 20:00
        $operationalHours = [9, 10, 11, 13, 14, 15, 16, 17, 18, 19];

        $totalDays = $start->diffInDays($end);
        $slots = [];
        $usedDays = [];

        // Pastikan tidak semua hari terisi (bolong-bolong: skip 30-40% hari)
        $activeDays = collect(range(0, $totalDays))
            ->filter(fn($d) => rand(1, 10) > 3) // 70% hari aktif
            ->shuffle()
            ->take($count + 10) // ambil lebih, nanti trim
            ->values();

        $i = 0;
        foreach ($activeDays as $dayOffset) {
            if ($i >= $count) {
                break;
            }

            $date = $start->copy()->addDays($dayOffset);

            // Max 2-3 transaksi per hari
            $txPerDay = rand(1, 3);
            $usedHours = [];

            for ($t = 0; $t < $txPerDay && $i < $count; $t++) {
                // Pilih jam operasional yang belum dipakai hari ini
                $availableHours = array_diff($operationalHours, $usedHours);
                if (empty($availableHours)) {
                    break;
                }

                $hour = $availableHours[array_rand($availableHours)];
                $usedHours[] = $hour;

                $slots[] = $date->copy()->setHour($hour)->setMinute(rand(0, 59))->setSecond(rand(0, 59));

                $i++;
            }
        }

        // Sort chronological
        usort($slots, fn($a, $b) => $a->timestamp <=> $b->timestamp);

        return $slots;
    }
}
