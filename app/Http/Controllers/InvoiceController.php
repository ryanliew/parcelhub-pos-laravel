<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\User;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function page()
    {
    	return view('invoice.overview');
    }

    public function create()
    {
        return view('invoice.create');
    }

    public function index()
    {

    	$invoices = Invoice::with(['customer','payment'])->get();

    	$outstanding = 0.0;
    	$payment = 0.0;

    	return datatables()
    			->of($invoices)
    			->addColumn('payment', function(Invoice $invoice) {
                    return $invoice->payment->sum();
                })
    			->addColumn('outstanding', function(Invoice $invoice) {
                    return $invoice->total - $invoice->payment->sum();
                })
    			->toJson();   
    }

    public function validateInput()
    {
        
    }

    public function store()
    {
        $items = json_decode(request()->items);

        // dd($items);

        $user = User::find(request()->created_by);

        $invoice = Invoice::create([
            'subtotal' => request()->subtotal,
            'total' => request()->total,
            'tax' => request()->has('tax') ? request()->tax : 0.00,
            'paid' => request()->paid,
            'type' => request()->type,
            'payment_type' => request()->payment_type,
            'branch_id' => $user->current_branch,
            'terminal_no' => $user->current_terminal,
            'created_by' => $user->id,
            'discount_value' => request()->has('discount_value') ? request()->discount_value : 0.00,
            'discount_mode' => request()->discount_mode,
            'discount' => request()->has('discount') ? request()->discount : 0.00,
            'remarks' => request()->remarks,
            'customer_id' => request()->customer_id
        ]);

        foreach($items as $item)
        {
            $invoice->items()->create([
                'tracking_code' => $item->tracking_code,
                'description' => $item->description,
                'zone' => $item->zone,
                'weight' => $item->weight,
                'dimension_weight' => $item->dimension_weight,
                'height' => isset($item->height) ? $item->height : 0,
                'length' => isset($item->length) ? $item->length : 0,
                'width' => isset($item->width) ? $item->width: 0,
                'sku' => $item->sku,
                'tax' => $item->tax,
                'price' => $item->price,
                'courier_id' => isset($item->courier_id) ? $item->courier_id : 0,
                'product_id' => $item->product_id,
                'product_type_id' => $item->product_type_id,
                'total_price' => $item->total_price,
                'unit' => $item->unit
            ]);
        }
        //$invoice->items()->create($items);

        return json_encode(['message' => "Invoice created successfully", "id" => $invoice->id]);
    }
}
