<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Customer;

class CustomerController extends Controller
{
    public function list()
    {
        $branch = auth()->user()->current()->first();

    	return $branch->customers()->with('branch')->get();
    }

    public function page()
    {
    	return view('customer.overview');
    }

    public function index()
    {

        $obj ='';

        if(auth()->user()->is_admin)
        {
            $obj  = datatables()->of(Customer::all())->toJson(); 
        }
        else
        {
            $branch = auth()->user()->current()->first();

            $obj = datatables()->of($branch->customers()->with('branch'))->toJson();
        }

    
        return $obj;
    }

    public function create()
    {
        return view('customer.create');
    }

    public function validate_input()
    {
        request()->validate([
            //"branch" => 'required',
            "name" => "required",
            "type" => "required",
            "contact" => "required",
        ]);
    }

    public function store()
    {
        $this->validate_input();

        $customer = Customer::create(request()->all());

        return json_encode(['message' => "Customer created" ]);
    }

    public function update(Customer $customer)
    {
        $this->validate_input();

        $customer->update(request()->all());
        
        return json_encode(['message' => "Customer updated"]);
    }

    public function get($customer)
    {
        $result = Customer::Where('id', $customer)->get();

        return $result;
    }
}
