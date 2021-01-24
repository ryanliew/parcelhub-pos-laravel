<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;

class Billing extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany("App\BillingItem");
    }

    public function branch()
    {
        return $this->belongsTo("App\Branch");
    }

    public function exportAndStoreExcel()
    {
        $name = now()->toDateString() . "_" . $this->branch->code . "_billing";

        $billing_id = $this->id;

        Excel::create($name, function($excel) use ($billing_id){
            $excel->sheet("Billing", function($sheet) use ($billing_id) {

                $billing = Billing::find($billing_id);
                $sheet->loadView("billing.table", ["billing" => $billing]);
            });
        })->store("xls", storage_path("app/public/billing/" . $this->branch_id));
    }
}
