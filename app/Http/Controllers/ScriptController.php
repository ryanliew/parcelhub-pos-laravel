<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\Branch;
use App\Customer;

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
	
	public function update_customers_outstanding_amount()
    {
		foreach(Customer::all() as $customer) {

			$outstanding_amount = ( $customer->invoices()->count() > 0 ? $customer->invoices()->sum('total') : 0 ) - 
								( $customer->payments()->count() > 0 ? $customer->payments()->sum('total') : 0 );
    		$customer->update(["outstanding_amount" => $outstanding_amount]);
    	}
    }
}
