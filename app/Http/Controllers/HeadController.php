<?php

namespace App\Http\Controllers;

use App\Head;
use App\Member;
use App\Product;
use Illuminate\Http\Request;

class HeadController extends Controller
{
    public function list()
    {
    	return Head::all();
    }

    public function list_active()
    {
        return Head::whereNotNull('activated_at')
                    ->where(function($query){ 
                        return $query->whereNull('deactivated_at')
                                    ->orWhereColumn('activated_at', '>', 'deactivated_at');
                                })
                    ->get();
    }

    public function activate()
    {
    	$heads = collect(json_decode(request()->heads));

    	foreach($heads as $head_id) {
    		$head = Head::where('number', $head_id)->first();

            if(!$head) $head = Head::create(['number' => $head_id]);

    		$head->activate();
    	}

    	return ["message" => "Activated headcounts " . $heads->implode(",")];
    }

    public function deactivate()
    {
    	$heads = collect(json_decode(request()->heads));

        $headcounts = Head::whereIn('id', $heads->pluck('id'))->get();

        $items = collect();

        foreach($headcounts as $key => $head) {
            $head->deactivate();

            $hours = $head->deactivated_at->diffInSeconds($head->activated_at) / 60;

            if(isset($heads[$key]->product_id))
            {
                $product = Product::find($heads[$key]->product_id)->load('tax');
            }
            else
            {
                $product = Product::headcounts()
                            ->with('tax')
                            ->where("hour_start", "<=", $hours)
                            ->where("hour_end", ">=", $hours)
                            ->orderByDesc("hour_end")
                            ->get()
                            ->first();
            }


            if($product) {

                $product->actual_hours = $hours;

                if(isset($heads[$key]->member)) {
                    $product->description .= " (Member: " . $heads[$key]->member->identifier . ")";
                    $product->price = $product->member_price;

                    $member = Member::find($heads[$key]->member->id);
                    $member->visits()->create([
                        'hours' => $hours
                    ]);
                }

                $items->push($product);
            }
        }

        return ["message" => "Deactivated headcounts " . $heads->implode("id", ","), "heads" => $headcounts, "products" => $items];
    }

    public function place_order(Head $head)
    {
         // Get current active session
        $current_session = $head->active_session()->first();

        // Get current user
        $branch = auth()->user()->current;

        // Add all items into the invoice
        foreach(json_decode(request()->items) as $item) {
            $current_session->items()->create([
                'description' => $item->description,
                'sku' => $item->sku,
                'tax' => $item->tax_value,
                'price' => $item->price,
                'member_price' => $item->member_price,
                'product_id' => $item->id,
                'product_type_id' => $item->product_type_id,
                'total_price' => $item->total,
                'unit' => $item->unit,
                'is_custom_pricing' => false,
                'tax_rate' => $item->tax->percentage,
                'is_tax_inclusive' => $item->is_tax_inclusive ? true: false,
                'tax_type' => $item->tax->code,
            ]);
        }

        // Return the invoice with items
        return ['message' => 'Order placed successfully', 'session' => $current_session->load('items')];
    }

    public function getGamingProduct()
    {
        $heads = collect(json_decode(request()->heads));
        // Get selected gaming
        $gaming = Product::find(request()->gaming_id);

        // Determine gaming item if it is auto
        if(!$gaming) {
            // We get the person that is activated the earliest
            $head = $heads->min('activated_at'); 

            // Get the difference in minutes
            $hours = now()->diffInSeconds($head->activated_at) / 60;

            // Get the product
            $gaming = Product::headcounts()
                        ->with('tax')
                        ->where("hour_start", "<=", $hours)
                        ->where("hour_end", ">=", $hours)
                        ->orderByDesc("hour_end")
                        ->get()
                        ->first();
        }
    }
}
