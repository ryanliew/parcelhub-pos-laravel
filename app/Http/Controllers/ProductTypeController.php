<?php

namespace App\Http\Controllers;

use App\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function products(ProductType $type)
    {
        return $type->products;
    }

    public function page()
	{
		return view('admin.product_types');
	}

	public function validate_input()
	{
		request()->validate([
            "name" => "required",
            "default_vendor_id" => "integer",
            "default_zone_type_id" => "integer"
        ]);
	}

    public function index()
    {
    	return datatables()->of(ProductType::all())->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $branch = ProductType::create([
            'name' => request()->name,
            'is_document' => request()->has('is_document'),
            'is_merchandise' => request()->has('is_merchandise'),
            'default_vendor_id' => request()->default_vendor_id,
            'default_zone_type_id' => request()->default_zone_type_id,
            'has_detail' => request()->has('has_detail')
        ]);

    	return json_encode(['message' => "New product type created."]);
    }

    public function update(ProductType $producttype)
    {
    	$this->validate_input();

        $producttype->update([
            'name' => request()->name,
            'is_document' => request()->has('is_document'),
            'is_merchandise' => request()->has('is_merchandise'),
            'default_vendor_id' => request()->default_vendor_id,
            'default_zone_type_id' => request()->default_zone_type_id,
            'has_detail' => request()->has('has_detail')
        ]);
    	
    	return json_encode(['message' => "Product type updated"]);
    }

    public function list()
    {
        return ProductType::orderBy('name', 'asc')->with('products.tax')->get();
    }
}
