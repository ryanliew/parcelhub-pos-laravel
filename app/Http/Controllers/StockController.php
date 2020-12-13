<?php

namespace App\Http\Controllers;

use App\Stock;
use App\Invoice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel as Excel;

class StockController extends Controller
{
    public function page()
	{
		return view('admin.stock');
	}

	public function validate_input()
	{
		request()->validate([
            "inventory_id" => "required",
        ]);
	}

    public function index()
    {	
        return datatables()->of(Stock::all())
        ->addColumn('inventory_name', function(Stock $stock){
            return is_null($stock->inventory_id) ? "---" : $stock->inventory->name;
        })
        ->toJson();
    }

    public function store()
    {
    	$this->validate_input();
        $stock = Stock::create(['date' => request()->date,
                                'quantity' => request()->quantity,
                                'type' => request()->type,
                                'active' => request()->has('active'),
                                'invoice_no' => request()->invoice_no,
                                'inventory_id' => request()->inventory_id ]);

    	return json_encode(['message' => "New stock created."]);
    }

    public function update(Stock $stock)
    {
    	$this->validate_input();

        $stock->update(['date' => request()->date,
                        'quantity' => request()->quantity,
                        'type' => request()->type,
                        'active' => request()->active,
                        'invoice_no' => request()->invoice_no,
                        'inventory_id' => request()->inventory_id ]);
    	
    	return json_encode(['message' => "Stock updated"]);
    }

    public function delete(Stock $stock)
    {
        $stock->delete();    	
    	return json_encode(['message' => "Stock deleted"]);
    }
    
    public function list()
    {
        return Stock::all();
    }    
}
