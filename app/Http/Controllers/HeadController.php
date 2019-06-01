<?php

namespace App\Http\Controllers;

use App\Head;
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
        foreach($headcounts as $head) {
            $head->deactivate();
        }

        return ["message" => "Deactivated headcounts " . implode(",", $heads), "heads" => $headcounts];
    }
}
