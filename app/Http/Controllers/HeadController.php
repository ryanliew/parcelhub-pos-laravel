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

    public function activate()
    {
    	$heads = collect(json_decode(request()->heads));

    	foreach($heads as $head_id) {
    		$head = Head::find($head_id->id);

    		$head->activate();
    	}

    	return ["message" => "Activated headcounts " . $heads->implode("id", ",")];
    }

    public function deactivate()
    {
    	$heads = collect(json_decode(request()->heads));

        $headcounts = Head::whereIn('id', $heads->pluck('id'))->get();

        $items = collect();

        foreach($headcounts as $key => $head) {
            $head->deactivate();

            $hours = $head->deactivated_at->diffInSeconds($head->activated_at) / 60;

            $product = Product::headcounts()
                        ->with('tax')
                        ->where("hour_start", "<=", $hours)
                        ->where("hour_end", ">=", $hours)
                        ->orderByDesc("hour_end")
                        ->get()
                        ->first();

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
}
