<?php

namespace App\Http\Controllers;

use App\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function page()
	{
		return view('admin.vendors');
	}

	public function validate_input()
	{
		request()->validate([
            "name" => "required",
            "formula" => "required",
            "zone_type_id" => "required"
        ]);
	}

    public function index()
    {
    	return datatables()->of(Vendor::with('zone_type'))->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $branch = Vendor::create(request()->all());

    	return json_encode(['message' => "New vendor created."]);
    }

    public function update(Vendor $vendor)
    {
    	$this->validate_input();

        $branch->update(request()->all());
    	
    	return json_encode(['message' => "Vendor updated"]);
    }

    public function list()
    {
        return Vendor::all();
    }
}
