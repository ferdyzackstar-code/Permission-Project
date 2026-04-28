<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReferenceProductSheet implements FromView, WithTitle, ShouldAutoSize
{
    protected $categories;

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    public function view(): View
    {
        return view('dashboard.products.sheets.reference_template', [
            'categories' => $this->categories,
        ]);
    }

    public function title(): string
    {
        return 'Daftar Referensi Products'; 
    }
}