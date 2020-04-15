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
       
        if(request()->export || request()->exportall)
        {
            $user = auth()->user()->id;
            $current_timestamp = str_slug(Carbon::now());
            $folder_name = 'Sales_reports_' . $user;
        }

        $all_branches_products = collect([]);
        $all_branches_vendors = collect([]);
        $all_branches_items = collect([]);
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
            $total_sales = $items->sum('total_price_after_discount');
            $products = $items->groupBy(function($item, $key){ return $item->product->sku; });

            $branch_products = ['products' => $products, 'branch' => $b];
            $branch_vendors = ['vendors' => $vendors, 'branch' => $b];
            $branch_items = ['items' => $items];
            $detail = ['vendors' => $vendors, 'products' => $products, 'items' => $items, 'branch' => $b, 'vendors_sum' => $vendors_sum, 'total_sales' => $total_sales];           

            if(!request()->exportall) // exportall only true when click download inside the all branches sales report, when download in dialog, excel are generated branch per branch
            {
                $all_branches_products = collect([]);
                $all_branches_vendors = collect([]);
                $all_branches_items = collect([]);
            }
            $all_branches_products->push($branch_products);
            $all_branches_vendors->push($branch_vendors);
            $all_branches_items->push($branch_items);

            if(request()->export)   
            {                
                $this->export_sales_report($vendors, $products, $items, $b, $from, $to, $folder_name, $all_branches_products, $all_branches_vendors, $all_branches_items);
            }

            $report_detail->push($detail);
        }
        
        if(request()->exportall)
        {
           $this->export_sales_report($vendors, $products, $items, $b, $from, $to, $folder_name, $all_branches_products, $all_branches_vendors, $all_branches_items);      
        }

        if(request()->export || request()->exportall)
        {
            return $this->compress_sales_report_in_zip($folder_name);
        }

        return view($report_view, ['report_detail' => $report_detail] ); 
        //return view('reports.sales', ['report_detail' => $report_detail] );
    }

    public function export_sales_report($vendors, $products, $items, $branch, $from, $to, $folder_name, $all_branches_products, $all_branches_vendors, $all_branches_items) 
    {
        $filename = "Sales Report (" . $from . " - " .  $to->toDateString() . ')';

        if($branch)
        {
            if (!file_exists(storage_path('exports/' . $folder_name))) {                
               mkdir(storage_path('exports/' . $folder_name), 0777, true);
            }

            if(request()->exportall)
            {
                $filename = $folder_name . '/' . $filename . " all branches";
            }
            else
            {
                $filename = $folder_name . '/' . $filename . " " . $branch->name;
            } 
        }

        return Excel::create($filename, function($excel) use ($vendors, $products, $items, $all_branches_products, $all_branches_vendors, $all_branches_items){ 
            $excel->sheet('Sales by product', function($sheet) use ($all_branches_products) {
                $sheet->loadView('reports.sheets.sales', ['all_branches_products' => $all_branches_products]);
            });

            $excel->sheet('Vendor sale', function($sheet) use ($all_branches_vendors){
                $sheet->loadView('reports.sheets.vendor', ['all_branches_vendors' => $all_branches_vendors]);
            });

            $excel->sheet('Detailed sales', function($sheet) use ($all_branches_items){
                $sheet->setColumnFormat([
                    'H' => '@'
                ]);
                $sheet->loadView('reports.sheets.detailed_sales', ['all_branches_items' => $all_branches_items]);
                
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
