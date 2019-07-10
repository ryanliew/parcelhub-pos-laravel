<?php

namespace App\Http\Controllers;

use App\Head;
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
    	$heads = json_decode(request()->heads);
    	foreach($heads as $head_id) {
    		$head = Head::find($head_id);

    		$head->activate();
    	}

    	return ["message" => "Activated headcounts " . implode(",", $heads)];
    }

    public function deactivate()
    {
    	$heads = json_decode(request()->heads);

        $headcounts = Head::whereIn('id', $heads)->get();

        $items = collect();

        foreach($headcounts as $head) {
            $head->deactivate();

            $hours = $head->deactivated_at->diffInMinutes($head->activated_at) / 60;

            $product = Product::headcounts()
                        ->with('tax')
                        ->where("hour_start", "<=", $hours)
                        ->where("hour_end", ">=", $hours)
                        ->orderByDesc("hour_end")
                        ->get()
                        ->first();

            $product->actual_hours = $hours;

            $items->push($product);
        }

        return ["message" => "Deactivated headcounts " . implode(",", $heads), "heads" => $headcounts, "products" => $items];
    }
}
