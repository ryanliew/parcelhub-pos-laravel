<?php

namespace App\Http\Controllers;

use App\InventoryProduct;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel as Excel;
use Illuminate\Support\Facades\Log;

class InventoryProductController extends Controller
{
    public function page()
	{
        return view('admin.inventory_products');
	}

	public function validate_input()
	{
		request()->validate([
            "inventory_id" => "required",
            "product_id" => "required",
        ]);
	}

    public function index()
    {	
        return datatables()
        ->of(InventoryProduct::all())
        ->addColumn('inventory_name', function(InventoryProduct $inventory_product){
                    return is_null($inventory_product->inventory_id) ? "---" : $inventory_product->inventory->name;
                    })
        ->addColumn('product_sku', function(InventoryProduct $inventory_product){
                     return is_null($inventory_product->product_id) ? "---" : $inventory_product->product->sku;
                    })
        ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $inventory_product = InventoryProduct::create(['inventory_id' => request()->inventory_id, 
                                                        'product_id' => request()->product_id, 
                                                        'quantity' => request()->quantity]);

    	return json_encode(['message' => "New inventory product created."]);
    }

    public function update(InventoryProduct $inventory_product)
    {
    	$this->validate_input();
        $inventory_product->update(['inventory_id' => request()->inventory_id, 
                                    'product_id' => request()->product_id, 
                                    'quantity' => request()->quantity]);
    	return json_encode(['message' => "Inventory product updated"]);
    }

    public function delete(InventoryProduct $inventory_product)
    {
        $inventory_product->delete();    	
    	return json_encode(['message' => "Inventory product deleted"]);
    }

    public function list()
    {
        return InventoryProduct::all();
    }    
}
