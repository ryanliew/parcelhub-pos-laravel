<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel as Excel;

class InventoryController extends Controller
{
    public function page()
	{
		return view('admin.inventory');
	}

	public function validate_input()
	{
		request()->validate([
            "name" => "required",
        ]);
	}

    public function index()
    {	
        return datatables()->of(Inventory::all())->toJson();
    }

    public function store()
    {
    	$this->validate_input();

        $inventory = Inventory::create(['name' => request()->name]);

    	return json_encode(['message' => "New inventory created."]);
    }

    public function update(Inventory $inventory)
    {
    	$this->validate_input();

        $inventory->update(['name' => request()->name]);
    	
    	return json_encode(['message' => "Inventory updated"]);
    }
    
	public function delete(Inventory $inventory)
    {
        $inventory->inventoryProducts()->delete();
        $inventory->stocks()->delete();
        $inventory->delete();
    	
    	return json_encode(['message' => "Inventory deleted"]);
    }

    public function list()
    {
        return Inventory::all();
    }
}
