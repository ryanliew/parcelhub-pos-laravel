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
    public const STATUS_SENT = "sent";

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
        $billing_import = BillingImport::find($import_id);
        $grouped_records = $records->groupBy("invoice_no_self");

        if($branch) {
            try{
                DB::beginTransaction();
                    foreach($grouped_records as $invoice => $records) {
                        $billing = Billing::updateOrCreate([
                            "invoice_no" => $invoice,
                            "billing_import_id" => $import_id,
                            "vendor_name" => $billing_import->vendor_name,
                        ], [
                            "branch_id" => $branch->id,
                            "billing_start" => $billing_import->billing_start,
                            "billing_end" => $billing_import->billing_end,
                            "invoice_date" => $billing_import->invoice_date,
                        ]);

                        foreach ($records->sortBy("pickup_date") as $record) {
                            BillingItem::updateOrCreate([
                                "billing_id" => $billing->id,
                                "consignment_no" => $record->hawb,
                            ], [
                                "weight" => $record->weight,
                                "zone" => $record->destination,
                                "charges" => $record->total_bill_amount,
                                "subaccount" => $record->sub_account,
                                "posting_date" => $record->pickup_date,
                                "pl_9" => $record->job_type,
                            ]);

                            // Mark record as processed
                            $record->is_processed = true;
                            $record->save();
                        }

                        // Dispatch mail sending for billing PDF and Excel
                        // Export Excel file and store
                        $billing->exportAndStoreExcel();
                        $billing->exportAndStorePDF();
                    }
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
