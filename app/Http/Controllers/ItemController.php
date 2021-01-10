<?php

namespace App\Http\Controllers;

use App\Item;
use App\ZoneType;
use App\Zone;
use App\ProductType;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use App\Branch;

class ItemController extends Controller
{
    //
    public function page()
	{
        if(auth()->user()->is_admin)
		  return view('admin.global_consignment_notes');

        return abort(403); 
    }
    
    public function index()
    {
        return datatables()->of(Item::query())
            ->addColumn('branch', function($item){
                return $item->invoice->branch? $item->invoice->branch->name : "";
            })
            ->addColumn('terminal', function($item){
                return $item->invoice->terminal? $item->invoice->terminal->name : "";
            })
            ->addColumn('invoice_no', function($item){
                return $item->invoice? $item->invoice->invoice_no : "";
            })
            ->addColumn('product_type', function($item){
                return ProductType::find($item->product_type_id) ? ProductType::find($item->product_type_id)->name : "";
            })
            ->addColumn('zone_type', function($item){
                return ZoneType::find($item->zone_type_id) ? ZoneType::find($item->zone_type_id)->name : "";
            })
            ->addColumn('zone', function($item){
                return Zone::find($item->zone) ? Zone::find($item->zone)->state : "";
            })
            ->toJson();	
    }
}
