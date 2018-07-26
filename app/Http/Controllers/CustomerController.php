<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Customer;
use App\Invoice;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use NumberFormatter;

class CustomerController extends Controller
{
    public function list()
    {
        $branch = auth()->user()->current()->first();

    	return $branch->customers()->with('branch')->get();
    }

    public function page()
    {
    	return view('customer.overview');
    }

    public function index()
    {

        $obj ='';

        if(auth()->user()->is_admin)
        {
            $obj  = datatables()->of(Customer::with('branch'))->toJson(); 
        }
        else
        {
            $branch = auth()->user()->current()->first();

            $obj = datatables()->of($branch->customers()->with('branch'))->toJson();
        }

    
        return $obj;
    }

    public function create()
    {
        return view('customer.create');
    }

    public function validate_input()
    {
        request()->validate([
            "branch" => 'required',
            "name" => "required",
            "type" => "required",
            "contact" => "required",
        ]);
    }

    public function validate_input_statement()
    {
        request()->validate([
            "date_from" => 'required',
            "date_to" => 'required|date|after:date_from',
        ]);
    }

    public function store()
    {
        $this->validate_input();

        $customer = Customer::create(request()->all());

        return json_encode(['message' => "Customer created", "customer" => $customer]);
    }

    public function update(Customer $customer)
    {
        $this->validate_input();

        $customer->update(request()->all());
        
        return json_encode(['message' => "Customer updated"]);
    }

    public function get($customer)
    {
        $result = Customer::Where('id', $customer)->get();

        return $result;
    }

    public function view($customer_id, $start, $end)
    {

        $url = 'statements\soa_'. $customer_id . $start . $end . '.pdf';

        $path = storage_path($url );

        return response()->file($path);
    }


    public function statement(Customer $customer)
    {

        $this->validate_input_statement();

        $date_from = Request()->date_from;
        $date_to = Request()->date_to;

        $invoices = Invoice::with('customer')
                    ->where([
                             ['customer_id', $customer->id],
                             ['created_at', '>=', $date_from],
                             ['created_at', '<=', $date_to],
                            ])
                    ->get();

        $balance = 0.0;
        $credit  = 0.0;
        $debit = 0.0;

        $credit_count = 0;
        $debit_count = 0;

        $outstanding = ['current' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];

        foreach ($invoices as $key => $invoice ) 
        {

            if( $invoice['total'] > 0 )
            {
                $debit_count ++;
                $debit += $invoice['total'];
            }

            if( $invoice['paid'] > 0 )
            {
                $credit_count ++;
                $credit += $invoice['paid'];
            }

            $balance += $invoice['total'] - $invoice['paid'];
            $invoice['balance'] = $balance;

            $remaining = $invoice['total'] - $invoice['paid'];

            if( $remaining > 0 )
            {
                $invoice_month = date_format($invoice['created_at'],"m");
                $pass_1_month = date("m", strtotime("-1 months"));
                $pass_2_month = date("m", strtotime("-2 months"));
                $pass_3_month = date("m", strtotime("-3 months"));
                $pass_4_month = date("m", strtotime("-4 months"));
                $pass_5_month = date("m", strtotime("-5 months"));

                if( $invoice_month == date('m') )
                {
                    $outstanding['current'] += $remaining;
                }
                else if( $invoice_month == $pass_1_month )
                {
                    $outstanding['1'] += $remaining;
                }
                else if( $invoice_month == $pass_2_month )
                {
                    $outstanding['2'] += $remaining;
                }
                else if( $invoice_month == $pass_3_month )
                {
                    $outstanding['3'] += $remaining;
                }
                else if( $invoice_month == $pass_4_month )
                {
                    $outstanding['4'] += $remaining;
                }
                else if( $invoice_month == $pass_5_month )
                {
                    $outstanding['5'] += $remaining;
                }
                    
            }


        }

        $f = new NumberFormatter(locale_get_default(), NumberFormatter::SPELLOUT);

        $balance = number_format((float)$balance,2,'.','');

        $tempNum = explode( '.' , $balance );

        $convertedNumber = ( isset( $tempNum[0] ) ? $f->format( $tempNum[0] ) : '' );

        $convertedNumber = str_replace( ' and ' ,' ' ,$convertedNumber );

        $convertedNumber .= ( ( isset( $tempNum[0] ) and isset( $tempNum[1] ) )  ? ' and ' : '' );

        $convertedNumber .= ( isset( $tempNum[1] ) ? $f->format( $tempNum[1] ) .' cents' : '' );

        $balance_en = strtoupper( $convertedNumber . ' only' );

        $html = View::make('customer.statement', 
                         ["customer" => $customer,
                          "invoices" => $invoices,
                          "balance"  => $balance,
                          "credit"   => $credit,
                          "debit"    => $debit,
                          "debit_count" => $debit_count,
                          "credit_count" => $credit_count,
                          "balance_en" => $balance_en,
                          "outstanding" =>$outstanding,
                          "attendant" => auth()->user()->name,
                         ])
                ->render();

        $newPDF = new mPDF(['format' => 'Legal']);
        $newPDF->WriteHTML($html);
        $newPDF->setFooter('{PAGENO}/{nbpg}');

        $path = storage_path('statements\soa_' . $customer->id . request()->date_from . request()->date_to . '.pdf' );
        $newPDF->Output($path, Destination::FILE);

        return json_encode(["message" => "Statement created succesfully", 
                            "redirect_url" => $path, 
                            "id" =>$customer->id, 
                            "start" => request()->date_from, 
                            "end" => request()->date_to ]);
    }

}
