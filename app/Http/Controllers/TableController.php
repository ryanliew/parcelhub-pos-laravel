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

    public function place_order(Table $table)
    {
        // Get current active session
        $current_session = $table->sessions()->active()->first();

        // Get current user
        $branch = auth()->user()->current;

        // Get next invoice number
        $invoice_no = $branch->code . sprintf("%05d", ++$branch->sequence->last_id);

        // Create the invoice
        $items = collect(json_decode(request()->items));

        $invoice = $current_session->invoices()->create([
            'subtotal' => $items->sum(function($item){ return $item->price * $item->unit; }),
            'tax' => $items->sum('tax_value'),
            'total' => $items->sum('total'),
            'branch_id' => $branch->id,
            'terminal_no' => auth()->user()->current_terminal,
            'invoice_no' => $invoice_no,
            'created_by' => auth()->user()->id,
        ]);

        // Add all items into the invoice
        foreach($items as $item) {
            $invoice->items()->create([
                'description' => $item->description,
                'sku' => $item->sku,
                'tax' => $item->tax_value,
                'price' => $item->price,
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

        // Update the sequence
        $branch->sequence()->update(["last_id" => $branch->sequence->last_id]);

        // Return the invoice with items
        return ['message' => 'Order placed successfully', 'invoice' => $invoice->load('items')];
    }

    public function close(Table $table)
    {
        request()->validate([
            'paid' => 'required',
        ]);

        $active_session = $table->sessions()->active()->first();

        $active_session->update([
            'discount' => request()->discount_amount,
            'discount_value' => request()->discount_value,
            'discount_mode' => request()->discount_type,
            'paid' => request()->paid,
            'payment_type' => request()->payment_method,
            'is_active' => 0,
            "total" => request()->total,
            "rounding" => request()->rounding,
        ]);

        return json_encode(['message' => "Table closed successfully, redirecting to invoice list page", "id" => $active_session->id, "redirect_url" => "/sessions/" . $active_session->id . "/receipt"]);
    }

    public function check(Table $table)
    {
        $active_session = $table->sessions()->active()->first();

        $active_session->update([
            'discount' => request()->discount_amount,
            'discount_value' => request()->discount_value,
            'discount_mode' => request()->discount_type,
            "total" => request()->total,
            "rounding" => request()->rounding,
        ]);

        return json_encode(['message' => "Generating guest check", 'redirect_url' => "/sessions/" . $active_session->id . "/check"]);
    }
}
