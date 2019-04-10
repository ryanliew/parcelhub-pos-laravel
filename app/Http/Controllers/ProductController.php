<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductType;
use App\Tax;
use App\Vendor;
use App\ZoneType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel as Excel;

class ProductController extends Controller
{
    public function page()
	{
		return view('admin.products');
	}

	public function validate_input()
	{
		request()->validate([
            "sku" => "required",
            "description" => "required",
            "zone" => "integer",
            "weight_start" => "numeric",
            "weight_end" => "numeric",
            "corporate_price" => "required|numeric",
            "walk_in_price" => "required|numeric",
            "walk_in_price_special" => "required|numeric",
            "vendor_id" => "integer",
            "product_type_id" => "required|integer",
            "tax_id" => "required|integer",
            "zone_type_id" => "integer"
        ]);
	}

    public function index()
    {
    	return datatables()->of(Product::with(["vendor", "zone_type", "product_type", "tax"])->select("products.*"))
                        ->addColumn('product_type_name', function(Product $product){
                            return $product->product_type->name;
                        })
                        ->addColumn('vendor_name', function(Product $product){
                            return is_null($product->vendor_id) ? "---" : $product->vendor->name;
                        })
                        ->addColumn('zone_type_name', function(Product $product){
                            return is_null($product->zone_type_id) ? "---" : $product->zone_type->name;
                        })
                        ->addColumn('tax_code', function(Product $product){
                            return $product->tax->code;
                        })
                        ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $product = Product::create($this->mutateValues());

    	return json_encode(['message' => "New SKU created."]);
    }

    public function update(Product $sku)
    {
    	$this->validate_input();

        $sku->update($this->mutateValues());
    	
    	return json_encode(['message' => "SKU updated"]);
    }

    public function mutateValues()
    {
        $values = request()->all();

        $values['is_tax_inclusive'] = request()->has('is_tax_inclusive');

        return $values;
    }

    public function import()
    {
        request()->validate([
            "file" => "required"
        ]);

        $excelRows= Excel::load(request()->file('file'))->noHeading()->skipRows(2)->toArray();

        $products = collect([]);

        foreach($excelRows as $excelRow) {

            $detail = [];
            $count = 0;
            
            if(!is_null($excelRow[0])) {
                $vendor = Vendor::where('name', $excelRow[2])->first();

                if(is_null($vendor) && !is_null($excelRow[2]) && $excelRow[2] !== "---")
                {
                    return $this->returnValidationErrorResponse(['file' => ['Courier ' . $excelRow[2] . ' not found. Please create it from the vendor page first']]);
                }


                $zone_type = ZoneType::where('name', $excelRow[4])->first();
                if(is_null($zone_type) && !is_null($excelRow[4]) && $excelRow[4] !== "---")
                {
                    return $this->returnValidationErrorResponse(['file' => ['Zone type ' . $excelRow[4] . ' not found. Please create it from the zone type page first']]);
                }


                $tax = Tax::where('code', $excelRow[10])->first();
                if(is_null($tax))
                {
                    return $this->returnValidationErrorResponse(array('file' => ['Tax code ' . $excelRow[10] . ' is invalid. Please create it from the tax type page first']));
                }


                $product_type = ProductType::where('name', $excelRow[1])->first();
                if(is_null($product_type))
                {
                    return $this->returnValidationErrorResponse(array('file' => ['ProductType ' . $excelRow[10] . ' is invalid. Please create it from the product type page first']));
                }

                $detail['sku'] = trim($excelRow[0]);
                $detail['description'] = $excelRow[12];
                $detail['zone'] = $excelRow[3];
                $detail['weight_start'] = $excelRow[5];
                $detail['weight_end'] = $excelRow[6];
                $detail['corporate_price'] = $excelRow[7];
                $detail['walk_in_price'] = $excelRow[8];
                $detail['walk_in_price_special'] = $excelRow[9];
                $detail['is_tax_inclusive'] = 1;
                $detail['vendor_id'] = is_null($vendor) ? null : $vendor->id;
                $detail['product_type_id'] = $product_type->id;
                $detail['tax_id'] = $tax->id;
                $detail['zone_type_id'] = is_null($zone_type) ? null : $zone_type->id;
                $products->push($detail);
            }
        }

        foreach($products as $product)
        {
            Product::updateOrCreate(["sku" => $product["sku"]],
                [
                    "sku" => $product['sku'],
                    "description" => $product['description'],
                    "zone" => $product['zone'],
                    "weight_start" => $product['weight_start'],
                    "weight_end" => $product['weight_end'],
                    "corporate_price" => $product['corporate_price'],
                    "walk_in_price" => $product['walk_in_price'],
                    "walk_in_price_special" => $product['walk_in_price_special'],
                    "is_tax_inclusive" => 1,
                    "vendor_id" => $product['vendor_id'],
                    "product_type_id" => $product['product_type_id'],
                    "tax_id" => $product['tax_id'],
                    "zone_type_id" => $product['zone_type_id']
                ]
            );

            $count++;
        }

        return ["message" => "Processed " . $count . " records"];
    }

    public function list()
    {
        $query = Product::with('vendor', 'product_type');


        if(!request()->has('full')) {
            if(request()->has('type')) 
                $query->where('product_type_id', request()->type);

            if(request()->zone != 0)
                $query->where('zone', request()->zone);
            else
                $query->whereNull('zone');


            if(request()->has('vendor'))
                $query->where('vendor_id', request()->vendor);

            if(request()->has('zonetype'))
                $query->where('zone_type_id', request()->zonetype);
            
            $weight = 0;
            if(request()->has('weight'))
                $weight = request()->weight;

            if(request()->has('dimension'))
                $weight = max($weight, request()->dimension);

            if(request()->has('weight') || request()->has('dimension'))
                $query->where('weight_start', "<=", $weight)
                    ->where('weight_end', ">=", $weight);
        }

        return $query->with('tax')->get();
    }
}
