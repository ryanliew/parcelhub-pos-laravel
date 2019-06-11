<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TableController;
use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function destroy(Item $item)
    {
    	$invoice = $item->invoice;

    	if($invoice->items->count() > 1) {
    		
    		$invoice->update([
    			'subtotal' => $invoice->subtotal - ($item->price * $item->unit),
	            'tax' => $invoice->tax - $item->tax,
	            'total' => $invoice->total - $item->total,
    		]);

    	} else {
    		$invoice->cancel();
    	}

    	$item->delete();

    	$tableController = new TableController();

    	return $tableController->current_items($invoice->session->table);
    }
}
