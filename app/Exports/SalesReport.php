<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReport implements FromView, WithTitle
{

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function view(): View
    {
        return view('reports.sheets.sales', ['products' => $this->products]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Sales by Product';
        // ({{ request()->from }} - {{ request()->to }}) - {{ $branch->name }}';
    }
}