<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class VendorReport implements FromView, WithTitle
{

    public function __construct($vendors)
    {
        $this->vendors = $vendors;
    }

    public function view(): View
    {
        return view('reports.sheets.vendor', ['vendors' => $this->vendors]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Vendor sale';
        // ({{ request()->from }} - {{ request()->to }}) - {{ $branch->name }}';
    }
}