<?php

namespace App\Http\Controllers;

use App\PaymentInvoice;
use App\Payment;
use Illuminate\Http\Request;

class PaymentInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($payment_id ='')
    {
        $payment = Payment::Find($payment_id);

        $result ='';

        if($payment)
        {
            $result = datatables()
                    ->of($payment->payments() )
                    ->toJson();  

        }

        return $result;
    }

    public function page($payment_id = '' )
    {
        $payment = Payment::Find($payment_id);

        $customer_name = $payment->customer ? $payment->customer->name : '';

        $type = $payment->payment_method;

        $total = $payment->total;

        return view('payment.detail', 
                    ['payment_id'   => $payment_id,
                     'payment_type' => $type,
                     'customer'     => $customer_name,
                     'total'        => $total,
                    ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentInvoice  $paymentInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentInvoice $paymentInvoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentInvoice  $paymentInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentInvoice $paymentInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentInvoice  $paymentInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentInvoice $paymentInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentInvoice  $paymentInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentInvoice $paymentInvoice)
    {
        //
    }
}
