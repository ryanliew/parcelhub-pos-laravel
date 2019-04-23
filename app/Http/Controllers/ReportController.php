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

        if(request()->has('branch'))
            $branch = Branch::find(request()->branch);

    	$invoices = $branch->invoices()->active()->whereBetween('created_at', [$from, $to->toDateString()])->get()->pluck('id');

    	$items = Item::with('product.vendor', 'product.product_type', 'invoice')->whereIn('invoice_id', $invoices)->get();

    	$vendors = $items->filter(function($item, $key){ return !is_null($item->product->vendor); })
                        ->groupBy(function($item, $key){ return $item->product->vendor->name; });
                        
        $vendors_sum = $vendors->sum(function($vendor)
        {
            return $vendor->sum('total_price');
        });

        $products = $items->groupBy(function($item, $key){ return $item->product->sku; });

        if(request()->export)
        {
            return $this->export_sales_report($vendors, $products, $items, $branch, $from, $to);
        }

    	return view('reports.sales', ['vendors' => $vendors, 'products' => $products, 'items' => $items, 'branch' => $branch, 'vendors_sum' => $vendors_sum]);
    }

    public function export_sales_report($vendors, $products, $items, $branch, $from, $to) 
    {

        $report = new SalesExport($vendors, $products, $items, $branch);

        $filename = "Sales Report (" . $from . " - " .  $to->toDateString() . ')';

        if($branch) $filename = $filename . " " . $branch->name;

        $filename = $filename . '.xlsx';

        return Excel::download($report, $filename);
    }
}
