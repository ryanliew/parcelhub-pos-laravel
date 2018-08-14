<?php

namespace App\Http\Controllers;

use App\Branch;
use App\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            "product_type_id" => "required"
        ]);
	}

    public function index()
    {
    	return datatables()->of(Branch::all())
                            ->addColumn('default_product_type_name', function($branch){
                                return ProductType::find($branch->product_type_id)->name;
                            })
                            ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $branch = Branch::create(request()->all());

        $branch->create_default_user();

        $branch->create_default_terminal();

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

    public function getDefaultValues()
    {
        $branch_code = auth()->user()->current->code;

        $results = DB::table('branch_knowledge')
                    ->select("*")
                    ->where(function($query) use ($branch_code) {
                        $query->where('branch_code', '=', $branch_code)
                            ->orWhere('branch_code', '=', '*');
                    })
                    ->where(function($query){
                        $query->where('product_type', '=', request()->type)
                            ->orWhere("product_type", '=', '*');
                    })
                    ->get();

        $result = $results->sortBy(function ($knowledge, $key){
            return ($knowledge->branch_code == "*") + ($knowledge->product_type == "*");
        });

        return json_encode(['result' => $result->first()]);
    }

    public function getDefaultType()
    {
        return json_encode(['type' => auth()->user()->current->product_type_id]);
    }

    public function getPricing()
    {
        $result = DB::table('branch_product')
                    ->select('corporate_price', 'walk_in_price', 'walk_in_price_special')
                    ->where('product_id', '=', request()->product);

        if(request()->has('customer')) {
            $result->whereRaw('customer_id = ' . request()->customer .' OR ISNULL(customer_id)');
        }
        else {
            $result->whereRaw('ISNULL(customer_id)');
        }


        if($result->orderBy('customer_id', 'DESC')->get()->count() == 0)
            $result = DB::table('products')
                        ->select('corporate_price', 'walk_in_price', 'walk_in_price_special', 'tax')
                        ->where('id', request()->product)
                        ->get();
        
        return json_encode($result->first());
    }   
}
