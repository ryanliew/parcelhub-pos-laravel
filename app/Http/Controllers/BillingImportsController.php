<?php

namespace App\Http\Controllers;

use App\BillingRecord;
use App\Imports\BillingImport;
use App\BillingImport as BillingImportClass;
use App\Jobs\ProcessBillingImports;
use App\Notifications\BillingReady;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PHPExcel_Shared_Date;

class BillingImportsController extends Controller
{
    public function page()
    {
        return view("billing.view");
    }

    public function import(BillingImport $import)
    {
        request()->validate([
            "file" => "required",
            "invoice_date" => "required|date",
            "billing_start" => 'required|date',
            "billing_end" => "required|date",
            "vendor_name" => "required",
        ]);

        $billing_import = BillingImportClass::latest()->first();

        $billing_import->total = $import->getTotalRowsOfFile() - 1; // Minus 1 to dismiss the header row
        $billing_import->save();

        $import->chunk(251, function($results) use ($billing_import)
        {
            foreach($results as $result) {
                BillingRecord::create([
                    "billing_imports_id" => $billing_import->id,
                    "sub_account" => $result->sub_account,
                    "invoice_no_ext" => $result->invoice_no,
                    "hawb" => $result->hawb,
                    "pickup_date" => date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($result->pick_up_date)),
                    "ref" => $result->ref,
                    "job_type" => $result->job_type,
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
                    "base_amount" => round($result->base_amount, 2),
                    "surcharge" => $result->surcharge,
                    "total_bill_amount" => round($result->total_bill_amount, 2),
                ]);
            }

            $billing_import->progress += $results->count();
            if($billing_import->progress >= $billing_import->total) {
                $billing_import->status = BillingImportClass::STATUS_PROCESSING;

            }
            $billing_import->save();
        });

        dispatch(new ProcessBillingImports($billing_import));

        return json_encode(['message' => "Billing file has been imported and it is being processed."]);
    }

    public function index()
    {
        $query = BillingImportClass::with([])->select('billing_imports.*')->latest();

        return datatables()->of($query)
            ->toJson();
    }

    public function view(BillingImportClass $billing)
    {
        return view("billing.detail", ["billing" => $billing]);
    }

    public function download(BillingImportClass $import)
    {
        // Pack the files into a zip and send out
        $fileName = "billing_import_" . $import->id . "_" . $import->vendor . "_" . $import->created_at->toDateString() . ".zip";

        $zipFile = storage_path($fileName);

        $zip = new \ZipArchive;

        if($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
            foreach($import->bills as $bill) {
                $pdf_url = storage_path("app/public/billing/" . $bill->branch_id . "/" . $bill->file_name . ".pdf");
                $zip->addFromString($bill->file_name . ".pdf", file_get_contents($pdf_url));

                $excel_url = storage_path("app/public/billing/" . $bill->branch_id . "/" . $bill->file_name . ".xls");
                $zip->addFromString($bill->file_name . ".xls", file_get_contents($excel_url));
            }
            $zip->close();
        }
        else {
            dd($zip->open($zipFile, \ZipArchive::CREATE));
        }

        Storage::disk("public")->put("billing/$fileName", file_get_contents($zipFile));

        return redirect(Storage::disk("public")->url("billing/$fileName"));
    }

    public function send(BillingImportClass $import)
    {
        // Trigger sending the emails to branches
        foreach($import->bills as $bill) {
            if($bill->branch->contact_emails) Notification::route("mail", $bill->branch->contact_emails)->notify(new BillingReady($bill));
        }

        $import->update([
            "status" => BillingImport::STATUS_SENT,
        ]);

        return response()->json([
            "message" => "Billing has been sent"
        ]);
    }

    public function delete(BillingImportClass $import)
    {
        $import->records()->delete();

        foreach($import->bills as $bill)
        {
            $bill->items()->delete();
        }

        $import->bills()->delete();
        $import->delete();

        return response()->json([
            "message" => "Billing has been deleted"
        ]);
    }
}
