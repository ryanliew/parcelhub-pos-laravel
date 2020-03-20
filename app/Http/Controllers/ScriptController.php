<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\Branch;

class ScriptController extends Controller
{
    public function move_invoices()
    {
    	$invoices = Invoice::where('created_by', 26)->where('terminal_no', 9)->where('invoice_no', ">=", "TEST00188")->get();

    	foreach($invoices as $invoice)
    	{
    		$branch = Branch::find(16)->load('sequence');
    		$number = $branch->code . sprintf("%05d", ++$branch->sequence->last_id);

    		$invoice->update([
    			'terminal_no' => 17,
    			'invoice_no' => $number,
    			'branch_id' => 16,
    		]);

    		$branch->sequence()->update(["last_id" => $branch->sequence->last_id]);
    	}

    	return $invoices->first()->id . " , " . $invoices->sortByDesc('created_at')->first()->id;
    }
}
