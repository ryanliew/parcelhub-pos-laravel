<?php

namespace App\Http\Controllers;

use App\Payment;
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

    
    public function page($invoice_no = '')
    {
        return view('payment.overview', ['invoice_no' => $invoice_no]);
    }

    public function receive()
    {
        return view('payment.receive' );
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

        $data = file_get_contents('php://input');

        // $data = json_decode($_POST['row'], true);

        $data = json_decode($data, true);

        $user = auth()->user();

        $customer = $data[0];
        $total = $data[1];
        $type = $data[2];

        if($total > 0 )
        {
            $payment = Payment::create([
                'customer_id' => $customer,
                'branch_id' => $user->current_branch,
                'terminal_no' => $user->current_terminal,
                'total' =>  $total,
                'payment_method' => $type,
                'created_by' => $user->id,
            ]);

            foreach ($data[3] as $row) {

                $invoice_total = $row[1];

                if( $invoice_total > 0 )
                {
                    PaymentInvoice::create([
                    'payment_id' => $payment->id,
                    'invoice_no' => $row[0],
                    'total' => $invoice_total,
                    ]);

                    $invoice = Invoice::where('invoice_no', $row[0])->get()->first();

                    $invoice->update([
                        'paid' => request()->amount + $invoice->paid,
                    ]);
                }
            }
        }

        return json_encode(['message' => "Payment created"]);
    }

    public function update()
    {

    }
}
