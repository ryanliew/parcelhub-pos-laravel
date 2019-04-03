<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalesExport implements WithMultipleSheets
{
    use Exportable;
    
    public function __construct($vendors, $products, $items, $branch)
    {
        $this->vendors = $vendors;
        $this->products = $products;
        $this->items = $items;
        $this->branch = $branch;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        array_push($sheets, 
                    new VendorReport($this->vendors),
                    new SalesReport($this->products),
                    new DetailedSalesReport($this->items));

        return $sheets;
    }
}