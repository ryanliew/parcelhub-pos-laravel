<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
	public function page()
	{
		return view('admin.branches');
	}

	public function validate()
	{
		
	}

    public function index()
    {
    	return datatables()->of(Branch::all())->toJson();	
    }

    public function store()
    {
    	$this->validate();

    	return json_encode(['message' => "New branch created"]);
    }

    public function update()
    {
    	$this->validate();
    	
    	return json_encode(['message' => "Old branch updated"]);
    }
}
