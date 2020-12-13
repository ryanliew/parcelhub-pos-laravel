<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Exports\SalesExport;
use App\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $branch = Branch::where('id', auth()->user()->current_branch)->get();
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
            $folder_name = 'Sales_reports_' . $user . "_" . $current_timestamp;
        }

        $all_branches_products = collect([]);
        $all_branches_vendors = collect([]);
        $all_branches_skutypes = collect([]);
        $all_branches_items = collect([]);
        foreach($branch as $b)
        {
            $invoices = $b->invoices()->active()->whereBetween('created_at', [$from, $to->toDateString()])->get()->pluck('id');

            $items = Item::select(DB::raw("items.*, ROUND(items.total_price - (invoices.discount_value / invoices.subtotal * items.total_price), 2) as total_price_after_discount"))
                        ->with('product.vendor', 'product.product_type', 'invoice')
                        ->leftJoin("invoices", "invoice_id", "=", "invoices.id")
                        ->whereIn('invoice_id', $invoices)
                        ->get();


            $vendors = Item::select(
                            DB::raw("items.*, vendors.*,
                                ROUND(items.total_price - (invoices.discount_value / invoices.subtotal * items.total_price), 2) as total_price_after_discount"))
                        ->leftJoin("invoices", "invoice_id", "=", "invoices.id")
                        ->leftJoin("vendors", "vendors.id", "=", "courier_id")
                        ->whereIn('invoice_id', $invoices)
                        ->whereNotNull('courier_id')
                        ->get();
            $vendors = $vendors->groupBy("name");  

            $skutypes = Item::select(
                        DB::raw("items.*, product_types.*,
                                ROUND(items.total_price - (invoices.discount_value / invoices.subtotal * items.total_price), 2) as total_price_after_discount"))
                        ->leftJoin("invoices", "invoice_id", "=", "invoices.id")
                        ->leftJoin("product_types", "product_types.id", "=", "product_type_id")
                        ->whereIn('invoice_id', $invoices)
                        ->whereNotNull('product_type_id')
                        ->get();
            $skutypes = $skutypes->groupBy("name");

            $vendors_sum = $vendors->sum(function($vendor)
            {
                return $vendor->sum('total_price_after_discount');
            });

            $total_sales = $items->sum('total_price_after_discount');
            $products = $items->groupBy(function($item, $key){ return $item->product->sku; });

            $branch_products = ['products' => $products, 'branch' => $b];
            $branch_vendors = ['vendors' => $vendors, 'branch' => $b];
            $branch_skutypes = ['skutypes' => $skutypes, 'branch' => $b];
            $branch_items = ['items' => $items];
            $detail = ['vendors' => $vendors, 'products' => $products, 'items' => $items, 'branch' => $b, 
                        'vendors_sum' => $vendors_sum, 'total_sales' => $total_sales
                    ,'skutypes' => $skutypes, 'skutypes_sum' => $vendors_sum
                ];           

            if(!request()->exportall) // exportall only true when click download inside the all branches sales report, when download in dialog, excel are generated branch per branch
            {
                $all_branches_products = collect([]);
                $all_branches_vendors = collect([]);
                $all_branches_skutypes = collect([]);
                $all_branches_items = collect([]);
            }
            $all_branches_products->push($branch_products);
            $all_branches_vendors->push($branch_vendors);
            $all_branches_skutypes->push($branch_skutypes);
            $all_branches_items->push($branch_items);

            if(request()->export)   
            {                
                $this->export_sales_report($vendors, $products, $items, $b, $from, $to, $folder_name, $all_branches_products, $all_branches_vendors, $all_branches_items, $all_branches_skutypes);
            }

            $report_detail->push($detail);
        }

        
        if(request()->exportall)
        {
           $this->export_sales_report($vendors, $products, $items, $b, $from, $to, $folder_name, $all_branches_products, $all_branches_vendors, $all_branches_items, $all_branches_skutypes, $report_detail);      
        }

        if(request()->export || request()->exportall)
        {
            return $this->compress_sales_report_in_zip($folder_name);
        }

        return view($report_view, ['report_detail' => $report_detail] ); 
        //return view('reports.sales', ['report_detail' => $report_detail] );
    }

    public function export_sales_report($vendors, $products, $items, $branch, $from, $to, $folder_name, $all_branches_products, $all_branches_vendors, $all_branches_items, $all_branches_skutypes, $report_detail = null) 
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

        return Excel::create($filename, function($excel) use ($vendors, $products, $items, $report_detail, $all_branches_products, $all_branches_vendors, $all_branches_skutypes, $all_branches_items){ 
            if($report_detail) {
                $excel->sheet('Sales by outlet', function($sheet) use ($report_detail) {
                    $sheet->loadView('reports.sheets.total_sales_by_outlet', ['report_details' => $report_detail]);
                });
            }

            $excel->sheet('Sales by product', function($sheet) use ($all_branches_products) {
                $sheet->loadView('reports.sheets.sales', ['all_branches_products' => $all_branches_products]);
            });

            $excel->sheet('Vendor sale', function($sheet) use ($all_branches_vendors){
                $sheet->loadView('reports.sheets.vendor', ['all_branches_vendors' => $all_branches_vendors]);
            });

            $excel->sheet('SKU types sale', function($sheet) use ($all_branches_skutypes){
                $sheet->loadView('reports.sheets.skutype', ['all_branches_skutypes' => $all_branches_skutypes]);
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
