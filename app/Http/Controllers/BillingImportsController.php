<?php

namespace App\Http\Controllers;

use App\BillingRecord;
use App\Imports\BillingImport;
use App\BillingImport as BillingImportClass;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BillingImportsController extends Controller
{
    public function page()
    {
        return view("billing.view");
    }

    public function import(BillingImport $import)
    {
        request()->validate([
            "file" => "required"
        ]);

        $billing_import = BillingImportClass::latest()->first();

        $billing_import->total = $import->getTotalRowsOfFile() - 1; // Minus 1 to dismiss the header row
        $billing_import->save();

        $import->chunk(251, function($results) use ($billing_import)
        {
            $billing_import = BillingImportClass::latest()->first();

            foreach($results as $result) {
                BillingRecord::create([
                    "billing_imports_id" => $billing_import->id,
                    "sub_account" => $result->sub_account,
                    "invoice_no_ext" => $result->invoice_no,
                    "hawb" => $result->hawb,
                    "pickup_date" => $result->pick_up_date,
                    "ref" => $result->ref,
                    "shipper_origin" => $result->shipper_origin,
                    "special_zone" => $result->special_zone,
                    "destination" => $result->destination,
                    "prod_type" => $result->prod_type,
                    "weight" => $result->weight,
                    "pcs" => $result->pcs,
                    "shipping_charge" => $result->shipping_charge,
                    "fsc" => $result->fsc,
                    "net_charges" => $result->net_charges,
                    "sst_rate" => $result->sst_rate,
                    "amount" => $result->amount,
                    "lc_marking" => $result->lc_marking,
                    "invoice_no_self" => $result->inv_no,
                    "base_amount" => $result->base_amount,
                    "surcharge" => $result->surcharge,
                    "total_bill_amount" => $result->total_bill_amount,
                ]);
            }

            $billing_import->progress += $results->count();
            if($billing_import->progress >= $billing_import->total) {
                $billing_import->status = BillingImportClass::STATUS_PROCESSING;
                // Dispatch the processing job here

            }
            $billing_import->save();
        });

        return json_encode(['message' => "Billing file has been imported and it is being processed."]);
    }

    public function index()
    {
        $query = BillingImportClass::with([])->select('billing_imports.*');

        return datatables()->of($query)
            ->toJson();
    }

    public function view(BillingImportClass $billing)
    {
        return view("billing.detail", ["billing" => $billing]);
    }
}
