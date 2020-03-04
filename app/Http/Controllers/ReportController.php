<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\SalesExport;
use \Excel;

class ReportController extends Controller
{
    public function page()
    {
    	return view('admin.reports');
    }

    public function sales_report()
    {
    	$from = request()->from;
    	$to = Carbon::parse(request()->to)->addDay();

        $branch = auth()->user()->current;
        $report_view = 'reports.sales';

        $total_branches_sales = 0;

        if((request()->allbranch)  && !request()->has('branch')){
            $branch = Branch::all();
            $report_view = 'reports.sales_all_branches';
           
        }

        if(request()->has('branch'))
        {
            if(request()->branch == '0') //all branches
            {
                $branch = Branch::all();
            }
            else
            {
                $branch = Branch::where('id', '=', request()->branch)->get();
            }
        }
        $report_detail = collect([]);
       
        if(request()->export)
        {
            $user = auth()->user()->id;
            $current_timestamp = str_slug(Carbon::now());
            $folder_name = 'Sales_reports_' . $user . "_" . $current_timestamp;
        }

        foreach($branch as $b)
        {
            $invoices = $b->invoices()->active()->whereBetween('created_at', [$from, $to->toDateString()])->get()->pluck('id');
            $items = Item::with('product.vendor', 'product.product_type', 'invoice')->whereIn('invoice_id', $invoices)->get();
    	    $vendors = $items->filter(function($item, $key){ return !is_null($item->product->vendor); })
                        ->groupBy(function($item, $key){ return $item->product->vendor->name; });                 
            $vendors_sum = $vendors->sum(function($vendor)
            {
                return $vendor->sum('total_price_after_discount');
            });
            $products = $items->groupBy(function($item, $key){ return $item->product->sku; });

            $detail = ['vendors' => $vendors, 'products' => $products, 'items' => $items, 'branch' => $b, 'vendors_sum' => $vendors_sum];           

            if(request()->export)   
            {
                $this->export_sales_report($vendors, $products, $items, $b, $from, $to, $folder_name);
            }

            $report_detail->push($detail);
        }

        if(request()->export)
        {
            return $this->compress_sales_report_in_zip($folder_name);
        }

        return view($report_view, ['report_detail' => $report_detail] ); 
        //return view('reports.sales', ['report_detail' => $report_detail] );
    }

    public function export_sales_report($vendors, $products, $items, $branch, $from, $to, $folder_name) 
    {
        $filename = "Sales Report (" . $from . " - " .  $to->toDateString() . ')';

        if($branch)
        {
            if (!file_exists(storage_path('exports/' . $folder_name))) {                
               mkdir(storage_path('exports/' . $folder_name), 0777, true);
            }

            $filename = $folder_name . '/' . $filename . " " . $branch->name;
        }

        return Excel::create($filename, function($excel) use ($vendors, $products, $items){ 
            $excel->sheet('Sales by product', function($sheet) use ($products) {
                $sheet->loadView('reports.sheets.sales', ['products' => $products]);
            });

            $excel->sheet('Vendor sale', function($sheet) use ($vendors){
                $sheet->loadView('reports.sheets.vendor', ['vendors' => $vendors]);
            });

            $excel->sheet('Detailed sales', function($sheet) use ($items){
                $sheet->setColumnFormat([
                    'H' => '@'
                ]);
                $sheet->loadView('reports.sheets.detailed_sales', ['items' => $items]);
                
            });
        })->store('xlsx');
    }

    public function compress_sales_report_in_zip($folder_name)
    {
        $zip_file = $folder_name . '.zip';
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addEmptyDir(storage_path($folder_name));

        $path = storage_path('exports/' . $folder_name);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file)
        {
            // We're skipping all subfolders
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();

                // extracting filename with substr/strlen
                $relativePath = $folder_name . '/' . substr($filePath, strlen($path) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return response()->download($zip_file);
    }
}
