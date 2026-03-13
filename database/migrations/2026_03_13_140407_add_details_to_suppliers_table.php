<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('item_code')->nullable()->after('name'); // Kode Barang
            $table->string('item_name')->nullable()->after('item_code'); // Nama Barang
            $table->integer('last_qty')->default(0); // QTY terakhir masuk
            $table->decimal('purchase_price', 15, 2)->default(0); // Harga Beli
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            //
        });
    }
};
