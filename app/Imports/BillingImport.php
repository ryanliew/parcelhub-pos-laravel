<?php

namespace App\Imports;

use Illuminate\Support\Facades\Input;
use App\BillingImport as BillingImportClass;
use Illuminate\Support\Facades\Storage;

class BillingImport extends \Maatwebsite\Excel\Files\ExcelFile
{

    /**
     * @inheritDoc
     */
    public function getFile()
    {
        $filename = request()->file("file")->store("imports");

        BillingImportClass::create([
            "file_name" => request()->file("file")->getClientOriginalName(),
            "name" => $filename,
            "status" => BillingImportClass::STATUS_IMPORTING,
            "progress" => 0,
            "total" => 0,
            "user_id" => auth()->id(),
            "invoice_date" => request()->invoice_date,
            "billing_start" => request()->billing_start,
            "billing_end" => request()->billing_end,
            "vendor_name" => request()->vendor_name,
        ]);

        return storage_path("app/" . $filename);
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }
}