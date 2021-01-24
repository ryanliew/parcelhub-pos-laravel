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
            "product_type_id" => "required",
            "registered_company_name" => "required",
            "lc_code" => "required",
            "contact_emails" => "required",
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

        $terminal = $branch->create_default_terminal();

        $branch->create_default_sequence();

        $branch->create_default_user($terminal->id);

        $branch->grant_admin_permission();

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
        if(request()->has('customer')) {
            // Get from customer group if available
            $result = DB::table('customer_group_product')
                    ->select('customer_group_product.corporate_price', 'customer_group_product.walk_in_price', 'customer_group_product.walk_in_price_special', 'products.id', 'products.is_tax_inclusive', 'taxes.code')
                    ->leftJoin('products', 'products.id' , '=', 'customer_group_product.product_id')
                    ->leftJoin('taxes', 'taxes.id', '=', 'products.tax_id')
                    ->where('customer_group_product.product_id', request()->product)
                    ->where('customer_group_id', request()->customer);
        }
        else {
            // Else get from products table instead
            $result = DB::table('products')
                        ->select('corporate_price', 'walk_in_price', 'walk_in_price_special', 'taxes.percentage as tax', 'is_tax_inclusive', 'taxes.code')
                        ->leftJoin('taxes', 'taxes.id', '=', 'products.tax_id')
                        ->where('products.id', request()->product);
        }

        return json_encode($result->first());
    }

    public function getTerminals(Branch $branch)
    {
        return $branch->terminals;
    }
}
