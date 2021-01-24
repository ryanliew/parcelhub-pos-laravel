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

        $billing_import = BillingImportClass::create([
            "name" => $filename,
            "status" => BillingImportClass::STATUS_IMPORTING,
            "progress" => 0,
            "total" => 0,
            "user_id" => auth()->id(),
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