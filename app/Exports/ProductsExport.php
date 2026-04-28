<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ProductsExport implements FromView, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    public function view(): View
    {
        return view('dashboard.products.export_excel', [
            'products' => Product::with(['category.parent'])->get(),
        ]);
    }

    public function title(): string
    {
        return 'Daftar Produk Anda PetShop';
    }

    public function columnFormats(): array
    {
        return [
            'C' => '"Rp " #,##0',
        ];
    }
}
