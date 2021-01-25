<?php

namespace App;

use App\Jobs\ProcessBillingGroup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function bills()
    {
        return $this->hasMany("App\Billing");
    }

    public function processByGroup()
    {
        $groups = $this->records->groupBy("lc_marking");

        $last = $groups->keys()->last();
        foreach($groups as $key => $group) {
            dispatch(new ProcessBillingGroup($key, $group, $last, $this->id));
        }
    }

    public static function processBillingGroup($key, $records, $import_id)
    {
        $branch = Branch::where("lc_code", $key)->first();
        $month = Carbon::createFromFormat("d-M-y", $records->min("pickup_date"));
        if($branch) {
            try{
                DB::beginTransaction();
                $billing = Billing::create([
                    "branch_id" => $branch->id,
                    "billing_start" => $month->startOfMonth(),
                    "billing_end" => $month->endOfMonth(),
                    "invoice_no" => $records->min("invoice_no_self"),
                    "billing_import_id" => $import_id,
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
                DB::commit();

            } catch (Exception $ex) {

                DB::rollback();
                info($ex->getMessage());
            }

        } else {
            info("Processing for billing import failed. LC Code: " . $key . " not found.");
        }
    }

}
