<?php

namespace App\Http\Controllers;

use App\BranchKnowledge;
use Illuminate\Http\Request;

class BranchKnowledgeController extends Controller
{
    public function page()
	{
		return view('admin.branch_knowledge');
	}

	public function validate_input()
	{
		request()->validate([
            "branch_code" => "required",
            "product_type" => "required",
            "vendor_name" => "required",
            "zone_type" => "required",
        ]);
	}

    public function index()
    {
        $query = auth()->user()->is_admin 
                ? BranchKnowledge::all() 
                : BranchKnowledge::where('branch_code', auth()->user()->current->code);

    	return datatables()->of($query)
                        ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $knowledge = BranchKnowledge::create(request()->all());

    	return json_encode(['message' => "New knowledge created."]);
    }

    public function update(BranchKnowledge $knowledge)
    {
    	$this->validate_input();

        $knowledge->update(request()->all());
    	
    	return json_encode(['message' => "Knowledge updated"]);
    }

}
