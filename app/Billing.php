<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class Billing extends Model
{
    protected $guarded = [];

    protected $dates = ["billing_start", "billing_end", "invoice_date"];

    public function items()
    {
        return $this->hasMany("App\BillingItem", "billing_id");
    }

    public function branch()
    {
        return $this->belongsTo("App\Branch");
    }

    public function getFileNameAttribute()
    {
        return "INV_" . $this->invoice_no . "_" . $this->branch->code;
    }

    public function getExcelPathAttribute()
    {
        return url("storage/billing/" . $this->branch_id . "/" . $this->file_name . ".xls");
    }

    public function getPdfPathAttribute()
    {
        return url("storage/billing/" . $this->branch_id . "/" . $this->file_name . ".pdf");
    }

    public function exportAndStoreExcel()
    {
        $name = $this->file_name;

        $billing_id = $this->id;
        Excel::create($name, function($excel) use ($billing_id){
            $excel->sheet("Billing", function($sheet) use ($billing_id) {

                $billing = self::find($billing_id);
                $sheet->loadView("billing.table", ["billing" => $billing]);
                $sheet->setColumnFormat(array(
                    'C' => "0"
                ));
            });
        })->store("xls", storage_path("app/public/billing/" . $this->branch_id));
    }

    public function exportAndStorePDF()
    {
        $html = View::make('billing.export', ["billing" => $this])->render();
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $newPDF = $this->initializePDFObject();
        $chunks = explode("chunk", $html);
        foreach($chunks as $val) {
            $newPDF->WriteHTML($val);
        }
        $newPDF->setFooter('{PAGENO}/{nbpg}');

        $path = storage_path('app/public/billing/'. $this->branch_id . "/" . $this->file_name . '.pdf');
        $newPDF->Output($path, Destination::FILE);
    }

    public function initializePDFObject()
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        return new mPDF(['format' => 'Legal',
            "fontDir" => array_merge($fontDirs, [storage_path('fonts/')]),
            "fontdata" => $fontData + [
                'yaqihei' => [
                    'R' => 'MicrosoftYaqiHei-2.ttf'
                ]
            ],
            'default_font' => 'yaqihei'
        ]);
    }
}
