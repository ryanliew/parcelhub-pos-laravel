<?php

namespace App\Http\Controllers;

use App\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function page()
	{
		return view('admin.zones');
	}

	public function validate_input()
	{
		request()->validate([
            "state" => "required",
            "postcode_start" => "required|integer",
            "postcode_end" => "required|integer",
            "zone" => "required|integer"
        ]);
	}

    public function index()
    {
    	return datatables()->of(Zone::all())->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $branch = Zone::create(request()->all());

    	return json_encode(['message' => "New zone created."]);
    }

    public function update(Zone $zone)
    {
    	$this->validate_input();

        $zone->update(request()->all());
    	
    	return json_encode(['message' => "Zone updated"]);
    }
}
