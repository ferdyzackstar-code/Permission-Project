<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsImportTemplateExport implements WithMultipleSheets
{
    protected $categories;

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    public function sheets(): array
    {
        return [
            new ProductsImportSheet(),
            new ReferenceProductSheet($this->categories)
        ];
    }
}