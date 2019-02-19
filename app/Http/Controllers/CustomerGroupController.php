<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerGroup;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

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

    public function validate_create_product_input()
    {
        $message = ['product_id.unique' => "This product already exists for this group, please edit it instead."];

        request()->validate([
            "product_id" => [
                                "required", 
                                "integer", 
                                Rule::unique('customer_group_product')->where(function ($query){
                                    return $query->where('customer_group_id', request()->group_id);
                                })
                            ],
            // "walk_in_price" => "required",
            // "walk_in_price_special" => "required",
            "corporate_price" => "required",
        ], $message);
    }

    public function validate_update_product_input()
    {
        $message = ['product_id.exists' => "This product has already been deleted. Please refresh the page and try again."];

        request()->validate([
            "product_id" => [
                                "required", 
                                "integer", 
                                Rule::exists('customer_group_product')->where(function ($query){
                                    return $query->where('customer_group_id', request()->group_id);
                                })
                            ],
            // "walk_in_price" => "required",
            // "walk_in_price_special" => "required",
            "corporate_price" => "required",
        ], $message);
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

    public function view(CustomerGroup $group)
    {
        return view('group.products', ['group' => $group]);
    }

    public function getProducts(CustomerGroup $group)
    {
        $products = collect();

        foreach($group->products as $product) {
            $products->push([
                'id' => $product->id,
                'sku' => $product->sku,
                // 'walk_in_price' => $product->pivot->walk_in_price,
                // 'walk_in_price_special' => $product->pivot->walk_in_price_special,
                'corporate_price' => $product->pivot->corporate_price,
                'customer_group_id' => $product->pivot->customer_group_id
            ]);
        }

        return datatables()->of($products)
                            ->make(true);
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

        $group->sync_products();

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
        $groups = auth()->user()->current->groups;

        if(request()->has('branch'))
            $groups = CustomerGroup::where('branch_id', request()->branch)->get();

        return $groups;
    }

    public function get($group)
    {
        $result = CustomerGroup::where('id', $group)->with('customers')->get();

        return $result;
    }

    public function destroy(CustomerGroup $group)
    {
        $group->customers()->update([
            'customer_group_id' => null
        ]);

        $group->products()->detach();

        $group->delete();

        return json_encode(['message' => "Customer group deleted"]);
    }

    public function add_product(CustomerGroup $group)
    {
        $this->validate_create_product_input();

        $group->products()->attach(request()->product_id, 
                                    [
                                    // 'walk_in_price' => request()->walk_in_price, 
                                    // 'walk_in_price_special' => request()->walk_in_price_special, 
                                    'walk_in_price' => 0, 
                                    'walk_in_price_special' => 0, 
                                    'corporate_price' => request()->corporate_price
                                    ]);

        return json_encode(['message' => "Product added successfully"]);
    }

    public function update_product(CustomerGroup $group, $product)
    {
        $this->validate_update_product_input();

        $group->products()->updateExistingPivot($product, 
                                    [
                                    // 'walk_in_price' => request()->walk_in_price, 
                                    // 'walk_in_price_special' => request()->walk_in_price_special, 
                                    'walk_in_price' => 0, 
                                    'walk_in_price_special' => 0, 
                                    'corporate_price' => request()->corporate_price
                                    ]);

        return json_encode(['message' => "Product updated successfully"]);
    }

    public function delete_product(CustomerGroup $group, $product)
    {
        $group->products()->detach($product);

        return json_encode(['message' => "Product deleted successfully"]);
    }

    public function import(CustomerGroup $group)
    {
        request()->validate([
            "file" => "required"
        ]);

        $excelRows= Excel::load(request()->file('file'))->noHeading()->skipRows(2)->toArray();

        $products = collect([]);

        $detail = [];

        foreach($excelRows as $excelRow) {
            
            if(!is_null($excelRow[0])) {
                $product = Product::where('sku', $excelRow[0])->first();

                if(is_null($product) && !is_null($excelRow[0]) && $excelRow[0] !== "---")
                {
                    return $this->returnValidationErrorResponse(['file' => ['SKU ' . $excelRow[0] . ' not found. Please create it from the SKU page first']]);
                }

                $detail[$product->id] = ['corporate_price' => $excelRow[1], 
                                        'walk_in_price' => 0, 
                                        'walk_in_price_special' => 0,
                                        ];
            }
        }

        $group->products()->sync($detail);

        return ["message" => "Processed " . sizeof($detail) . " records"];
    }
}

