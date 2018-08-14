<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Customer;
use App\PaymentInvoice;
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

    public function receive()
    {

        $customer = Customer::select('id','name')->get();

        if(auth()->user()->is_admin)
        {
            $customer = Customer::select('id','name')->get();
        }
        else
        {
            $branch = auth()->user()->current()->first();

            $customer = $branch->customers()->get();
        }

        return view('payment.receive')->with(['customers'=>$customer]);
    }

    public function page()
    {
        return view('payment.overview');
    }

    public function index()
    {

        $result ='';

        if(auth()->user()->is_admin)
        {
            $result = datatables()->of(Payment::with(['customer','branch','terminal']) );
        }
        else
        {
            $branch = auth()->user()->current()->first();

            $result = datatables()->of($branch->payments()->with(['customer','branch','terminal']) );
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

        $data = file_get_contents('php://input');

        $data = json_decode($data, true);

        $user = auth()->user();

        $customer = $data[0];
        $total = $data[1];
        $type = $data[2];

        $payment_id = '';

        if($total > 0 )
        {
            $payment = Payment::create([
                'customer_id'   => $customer,
                'branch_id'     => $user->current_branch,
                'terminal_no'   => $user->current_terminal,
                'total'         =>  $total,
                'payment_method' => $type,
                'created_by'    => $user->id,
            ]);

            $payment_id = $payment->id;

            foreach ($data[3] as $row) {

                $amount = $row[1];

                if( $amount > 0 )
                {
                    $invoice = Invoice::where('invoice_no', $row[0])->get()->first();

                    $paid = $invoice->payment->sum('total') + $invoice->paid + $amount;

                    PaymentInvoice::create([
                        'payment_id'    => $payment_id,
                        'invoice_no'    => $row[0],
                        'total'         => $amount,
                        'invoice_total' => $row[3],
                        'outstanding'   => $row[2],
                        'paid'          => $paid
                    ]);

                    // $invoice->update([
                    //     'paid' => $paid,
                    // ]);
                }
            }
        }

        return json_encode(['message' => "Payment created",'payment_id' => $payment_id ] );
    }

}
