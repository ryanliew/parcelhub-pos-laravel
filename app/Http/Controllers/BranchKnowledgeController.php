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
    	return datatables()->of(BranchKnowledge::all())
                        ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $terminal = BranchKnowledge::create(request()->all());

    	return json_encode(['message' => "New knowledge created."]);
    }

    public function update(BranchKnowledge $terminal)
    {
    	$this->validate_input();

        $terminal->update(request()->all());
    	
    	return json_encode(['message' => "Knowledge updated"]);
    }

}
