<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function validate_input()
    {
        request()->validate([
            "invoice_no" => "required",
            "amount" => "required",
            "type" => "required",
        ]);
    }

    
    public function page($invoice_no = '')
    {
        return view('payment.overview', ['invoice_no' => $invoice_no]);
    }

    public function index($invoice_no = '')
    {

        $result ='';

        $invoice = Invoice::where('invoice_no', '=', $invoice_no)->first();

        if($invoice)
        {
            $result = datatables()
                    ->of($invoice->payment()->with(['customer','branch']) );

        }
        else
        {
            if(auth()->user()->is_admin)
            {
                $result = datatables()->of(Payment::with(['customer','branch']) );
            }
            else
            {
                $branch = auth()->user()->current()->first();

                $result = datatables()->of($branch->payments()->with(['customer','branch']) );
            }
                
        }

        $result = $result
                    ->addColumn('customer', function(Payment $payment){ 
                            return $payment->customer ? $payment->customer->name : "---";
                        })
                    ->addColumn('branch', function(Payment $payment){ 
                        return $payment->branch ? $payment->branch->name : "---";
                    })
                    ->toJson();  
    
        return $result;
    }


    public function store()
    {
        $this->validate_input();

        $user = auth()->user();

        $invoice = Invoice::find(request()->invoice_id);

        $payment = Payment::create([
            'customer_id' => $invoice->customer ? $invoice->customer->id : '',
            'branch_id' => $user->current_branch,
            'terminal_no' => $user->current_terminal,
            'total' =>  request()->amount,
            'payment_method' => request()->type,
            'created_by' => $user->id,
            'invoice_no' => request()->invoice_no,

        ]);


        $invoice->update([
            'paid' => request()->amount + $invoice->paid,
        ]);


        return json_encode(['message' => "Payment created"]);
    }

    public function update()
    {

    }
}
