<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function page()
    {
    	return view('admin.reports');
    }

    public function sales_report()
    {
    	$from = request()->from;
    	$to = request()->to;

    	$invoices = auth()->user()->current->invoices()->active()->whereBetween('created_at', [$from, $to])->get()->pluck('id');

    	$items = Item::with('product.vendor', 'product.product_type', 'invoice')->whereIn('invoice_id', $invoices)->get();

    	$vendors = $items->filter(function($item, $key){ return !is_null($item->product->vendor); })
    					->groupBy(function($item, $key){ return $item->product->vendor->name; });

    	$products = $items->groupBy(function($item, $key){ return $item->product->sku; });

    	return view('reports.sales', ['vendors' => $vendors, 'products' => $products, 'items' => $items]);
    }
}
