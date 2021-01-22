<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BillingImport extends Model
{
    public const STATUS_IMPORTING = "importing";
    public const STATUS_PROCESSING = "processing";
    public const STATUS_SENDING = "sending";
    public const STATUS_COMPLETE = "complete";

    protected $guarded = [];

    public function records()
    {
        return $this->hasMany("App\BillingRecord", "billing_imports_id");
    }

    public function processByGroup()
    {
        $groups = $this->records->groupBy("lc_marking");

        foreach($groups as $group) {
            // Dispatch process billing group here
        }
    }

    public static function processBillingGroup($key, $records)
    {
        $branch = Branch::where("lc_code", $key)->first();
        $month = Carbon::createFromFormat("d-M-y", $records->min(pickup_date));
        if($branch) {
            $billing = Billing::create([
                "branch_id" => $branch->id,
                "billing_start" => $month->startOfMonth(),
                "billing_end" => $month->endOfMonth(),
                "invoice_no" => $records->min("invoice_no_self"),
            ]);

            foreach ($records as $record) {
                BillingItem::create([
                    "billing_id" => $billing->id,
                    "consignment_no" => $record->hawb,
                    "weight" => $record->weight,
                    "zone" => $record->destination,
                    "charges" => $record->total_bill_amount,
                    "subaccount" => $record->subaccount,
                ]);
            }

            // Dispatch mail sending for billing PDF and Excel
        } else {
            error("LC Code: " . $key . " not found.");
        }
    }

}
