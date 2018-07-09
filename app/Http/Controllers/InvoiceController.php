<?php

namespace App\Http\Controllers;

use App\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function page()
    {
    	return view('invoice.overview');
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
}
