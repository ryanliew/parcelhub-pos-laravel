<?php

namespace App;

use App\Jobs\ProcessBillingGroup;
use App\Notifications\BillingReady;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

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

        $last = $groups->keys()->last();
        foreach($groups as $key => $group) {
            dispatch(new ProcessBillingGroup($key, $group, $last));
        }
    }

    public static function processBillingGroup($key, $records)
    {
        $branch = Branch::where("lc_code", $key)->first();
        $month = Carbon::createFromFormat("d-M-y", $records->min("pickup_date"));
        if($branch) {
            $billing = Billing::create([
                "branch_id" => $branch->id,
                "billing_start" => $month->startOfMonth(),
                "billing_end" => $month->endOfMonth(),
                "invoice_no" => $records->min("invoice_no_self"),
            ]);

            foreach ($records->sortBy("pickup_date") as $record) {
                if(!$record->is_processed) {
                    BillingItem::create([
                        "billing_id" => $billing->id,
                        "consignment_no" => $record->hawb,
                        "weight" => $record->weight,
                        "zone" => $record->destination,
                        "charges" => $record->total_bill_amount,
                        "subaccount" => $record->subaccount,
                        "posting_date" => $record->pickup_date
                    ]);

                    // Mark record as processed
                    $record->is_processed = true;
                    $record->save();
                }
            }

            // Dispatch mail sending for billing PDF and Excel
            // Export Excel file and store
            $billing->exportAndStoreExcel();
            $billing->exportAndStorePDF();

            Notification::route("mail", $billing->branch->contact_emails)->notify(new BillingReady($billing));

        } else {
            info("Processing for billing import failed. LC Code: " . $key . " not found.");
        }
    }

}
