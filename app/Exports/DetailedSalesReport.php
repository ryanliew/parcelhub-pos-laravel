<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DetailedSalesReport implements FromView, WithTitle
{

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function view(): View
    {
        return view('reports.sheets.detailed_sales', ['items' => $this->items]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Detailed sales';
        // ({{ request()->from }} - {{ request()->to }}) - {{ $branch->name }}';
        // return 'Month ' . $this->month;
    }
}