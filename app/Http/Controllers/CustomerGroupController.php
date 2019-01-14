<?php

namespace App\Http\Controllers;

use App\CustomerGroup;
use App\Customer;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function page()
	{
		return view('group.overview');
	}

	public function validate_input()
	{
		request()->validate([
            "name" => "required",
            "branch_id" => "required|integer"
        ]);
	}

    public function index()
    {
    	$query = auth()->user()->current->groups()->with('customers')->select('customer_groups.*');

    	return datatables()->of($query)
                        ->addColumn('customer_count', function(CustomerGroup $group){
                            return $group->customers->count();
                        })
                        ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

    	$group = CustomerGroup::create([
    		'name' => request()->name,
    		'branch_id' => request()->branch_id,
    	]);
    	
    	Customer::whereIn('id', collect(json_decode(request()->customers))->pluck('value'))
    			->update([
		    		'customer_group_id' => $group->id,
		    	]);

    	return json_encode(['message' => "New group created."]);
    }

    public function update(CustomerGroup $group)
    {
    	$this->validate_input();

        $group->update([
            'name' => request()->name,
        ]);
    	
    	Customer::where('customer_group_id', $group->id)->update([
    		'customer_group_id' => null,
    	]);

    	Customer::whereIn('id', collect(json_decode(request()->customers))->pluck('value'))
    			->update([
		    		'customer_group_id' => $group->id,
		    	]);

    	return json_encode(['message' => "Customer group updated"]);
    }

    public function list()
    {
        return auth()->user()->current->groups;;
    }

    public function get($group)
    {
        $result = CustomerGroup::where('id', $group)->with('customers')->get();

        return $result;
    }}
