<?php

namespace App\Http\Controllers;

use App\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function list()
    {
    	return auth()->user()->current->tables;
    }

    public function activate()
    {
    	$table = Table::find(request()->table_id);

    	$table->toggleStatus();

    	$message = $table->is_active ? 'activated' : 'deactivated';

    	return ["message" => "Table " . $message, "table" => $table];
    }

    public function getItems()
    {
    	$session = $table->sessions()->active()->first()->get();

    	$items = collect();

    	foreach($session->invoices as $invoice) {
    		$items->push($invoice->items);
    	}

    	$items = $items->flatten();

    	return $items;
    }

    public function current_items(Table $table)
    {
        return $table->sessions()->active()->get()->first()->invoices()->with('items')->get();
    }
}
