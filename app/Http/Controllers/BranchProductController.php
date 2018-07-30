<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class BranchProductController extends Controller
{
    public function page()
	{
		return view('pricing.overview');
	}

	public function validate_input()
	{
		request()->validate([
            "product_id" => "required",
            "customer_id" => "required",
            "corporate_override" => "required",
            "walk_in_special_override" => "required",
            "walk_in_override" => "required"
        ]);
	}

    public function index()
    {

    	return datatables()->of(auth()->user()->current->products()->get())
    					->addColumn('customer_name', function($product){
    						return Customer::find($product->pivot->customer_id)->name;
    					})
    					->addColumn('courier_name', function($product){
    						return !is_null($product->vendor) ? $product->vendor->name : "---";
    					})
    					->addColumn('last_update', function($product){
    						return $product->pivot->updated_at->toDateTimeString();
    					})
                        ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        auth()->user()->current->products()->attach([ 
        	request()->product_id => ['walk_in_price' => request()->walk_in_override,
        							'walk_in_price_special' => request()->walk_in_special_override,
        							'corporate_price' => request()->corporate_override,
        							'customer_id' => request()->customer_id,
        							'is_tax_inclusive' => true
        							]
        ]);

    	return json_encode(['message' => "Product price override for branch " . auth()->user()->current->name . " saved."]);
    }

    public function update()
    {
    	$this->validate_input();

        $branch_product = auth()->user()->current->products()->updateExistingPivot(
        	request()->product_id, 
        	['walk_in_price' => request()->walk_in_override,
			'walk_in_price_special' => request()->walk_in_special_override,
			'corporate_price' => request()->corporate_override,
			'customer_id' => request()->customer_id,
			'is_tax_inclusive' => true
			]
        );

    	return json_encode(['message' => "Product price override for branch " . auth()->user()->current->name . " saved."]);
    }

    public function destroy($product)
    {
    	auth()->user()->current->products()->detach($product);

    	return json_encode(['message' => "Product price override deleted"]);
    }
}
