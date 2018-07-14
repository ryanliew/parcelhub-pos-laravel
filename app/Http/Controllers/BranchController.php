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

	public function validate_input()
	{
		request()->validate([
            "name" => "required",
            "code" => "required",
            "owner" => "required",
            "contact" => "required",
            "email" => "required",
            "registration_no" => "required",
            "payment_bank" => "required",
            "payment_acc_no" => "required",
            "address" => "required",
            "terminal_count" => "required" 
        ]);
	}

    public function index()
    {
    	return datatables()->of(Branch::all())->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $branch = Branch::create(request()->all());

        $branch->create_default_user();

    	return json_encode(['message' => "New branch created. User " . $branch->code . " has been assigned to the branch."]);
    }

    public function update(Branch $branch)
    {
    	$this->validate_input();

        $branch->update(request()->all());
    	
    	return json_encode(['message' => "Branch updated"]);
    }

    public function list()
    {
        return Branch::all();
    }

    public function get($branch)
    {
        $result = Branch::where('id', $branch)->get();

        return $result;
    }
}
