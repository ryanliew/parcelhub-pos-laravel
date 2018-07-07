<?php

namespace App\Http\Controllers;

use App\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function page()
	{
		return view('admin.taxes');
	}

	public function validate_input()
	{
		request()->validate([
            "code" => "required",
            "percentage" => "required|integer"
        ]);
	}

    public function index()
    {
    	return datatables()->of(Tax::all())->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $branch = Tax::create(request()->all());

    	return json_encode(['message' => "New tax created."]);
    }

    public function update(Tax $tax)
    {
    	$this->validate_input();

        $tax->update(request()->all());
    	
    	return json_encode(['message' => "Tax updated"]);
    }

    public function list()
    {
        return Tax::all();
    }
}
