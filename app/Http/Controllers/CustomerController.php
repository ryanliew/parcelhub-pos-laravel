<?php

namespace App\Http\Controllers;


use App\Branch;
use App\Customer;
use App\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use NumberFormatter;

class CustomerController extends Controller
{
    public function list()
    {
        $branch_id = request()->has('branch') ? request()->branch : auth()->user()->current_branch;

        $branch = Branch::findOrFail($branch_id);

        $query = $branch->customers();
        
        if(request()->has('groups'))
            $query->whereNull('customer_group_id');
    	
        return $query->orderBy('name')->get();
    }

    public function page()
    {
    	return view('customer.overview');
    }

    public function index()
    {
        if(auth()->user()->is_admin)
        {
            $query = Customer::with('branch');
        }
        else
        {
            $branch = auth()->user()->current;

            $query = $branch->customers()->with('branch');
        }

    
        return datatables()->of($query)
                            ->addColumn('group_name', function($customer){
                                return $customer->group ? $customer->group->name : "---";
                            })
                            ->toJson();
    }

    public function create()
    {
        return view('customer.create');
    }

    public function validate_input()
    {
        request()->validate([
            "branch_id" => 'required',
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

        if(!request()->has('customer_group_id'))
            $customer->update(['customer_group_id' => null]);
        
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
        $date_to = Carbon::parse(Request()->date_to);

        $getAll = Request()->type == "All" ? true: false;

        $invoices = Invoice::with(['customer','payment'])
                    ->active()
                    ->where([
                             ['customer_id', $customer->id],
                             ['created_at', '>=', $date_from],
                             ['created_at', '<=', $date_to->addDays(1)->toDateString()],
                            ])
                    ->orderby('created_at')
                    ->get();

        $result = collect([]);
                       
        foreach($invoices as $invoice)
        {
            $outstanding = $invoice->total 
                            - $invoice->payment->sum('paid') 
                            - ($invoice->total > 0 
                                ? min($invoice->paid, $invoice->total) 
                                : max($invoice->paid, $invoice->total)
                            );

            
            
            if( $getAll || $outstanding != 0)
            {   
                $debit = [
                    'date' => $invoice->created_at, 
                    'total' => $invoice->total, 
                    'debit' => true, 
                    'balance' => 0,
                    'desc' => '',
                    'ref' => $invoice->invoice_no
                ];

                if($invoice->paid >= $invoice->total && $invoice->total > 0)
                {
                    $debit['paid'] = $invoice->total;
                }

                $result->push($debit);

                foreach($invoice->payment as $payment)
                {
                    $credit = [
                        'date' => $payment->created_at, 
                        'total' => - $payment->total, 
                        'debit' => false, 
                        'balance' => 0,
                        'desc' => $payment->payment->payment_method,
                        'ref'  => $payment->payment->ref
                    ];

                    $result->push($credit);
                }
            }
        }

        $sortedResult = $result->sortBy('date');
        $resultBalance = 0.0;

        foreach($sortedResult as $key => $collection)
        { 
            $paid = array_key_exists('paid', $collection) ? $collection['paid']: 0;

            // info($collection['ref']);
            // info($paid);

            $array = [
                'date' => $collection['date'],
                'balance'=> $resultBalance += $collection['total'] - $paid,
                'total' => $collection['total'],
                'debit' => $collection['debit'],
                'desc' => $collection['desc'], 
                'ref'  => $collection['ref']
            ];


            info($resultBalance);

            if($paid > 0)
                $array['paid'] = $paid;

            $sortedResult->put($key, $array);
        }

        $balance = 0.0;
        $credit  = 0.0;
        $debit = 0.0;

        $credit_count = 0;
        $debit_count = 0;

        $outstanding = ['current' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];

        foreach ($invoices as $key => $invoice ) 
        {
            $unpaid = $invoice['total'] - $invoice['paid'] - $invoice->payment->sum('total');

            if( $getAll || $unpaid > 0)
            {

                $debit_count ++;
                $debit += $invoice['total'];

                $credit_count += $invoice->payment->count('total');
                $credit += $invoice->payment->sum('total');
                
                $invoice_month = date_format($invoice['created_at'],"m");
                $pass_1_month = date('m', strtotime('-' . date('d') . ' days'));
                $pass_2_month = date("m", strtotime("-2 months"));
                $pass_3_month = date("m", strtotime("-3 months"));
                $pass_4_month = date("m", strtotime("-4 months"));
                $pass_5_month = date("m", strtotime("-5 months"));

                if( $invoice_month == date('m') )
                {
                    $outstanding['current'] += $unpaid;
                }
                else if( $invoice_month == $pass_1_month )
                {
                    $outstanding['1'] += $unpaid;
                }
                else if( $invoice_month == $pass_2_month )
                {
                    $outstanding['2'] += $unpaid;
                }
                else if( $invoice_month == $pass_3_month )
                {
                    $outstanding['3'] += $unpaid;
                }
                else if( $invoice_month == $pass_4_month )
                {
                    $outstanding['4'] += $unpaid;
                }
                else if( $invoice_month == $pass_5_month )
                {
                    $outstanding['5'] += $unpaid;
                }

            }
        }

        $f = new NumberFormatter(locale_get_default(), NumberFormatter::SPELLOUT);

        $resultBalance = number_format((float)$resultBalance,2,'.','');

        $tempNum = explode( '.' , $resultBalance );

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
                          "result" =>$sortedResult,
                          "resultBalance" => $resultBalance
                         ])
                ->render();

        $newPDF = new mPDF(['tempDir' => storage_path('mpdf'), 'format' => 'Legal']);
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

    public function statementMultiple()
    {
        $customers = collect(json_decode(request()->customers));
        $customers_id = $customers->pluck('id');

        foreach( Customer::whereIn('id', $customers_id)->get() as $customer ) {
           $this->statement($customer);
        }
        
        return json_encode(["message" => "Statement created succesfully",  
                            "id" =>$customers_id, 
                            "start" => request()->date_from, 
                            "end" => request()->date_to ]);       
    }

    public function customerTypeStatement()
	{
        if(!request()->has('data'))
            return view('customer.customer_type_statement');
        

        $date_from = request()->from;
        $date_to = Carbon::parse(request()->to);
        $isOutstanding = request()->type == "Outstanding" ? true: false;
        $isZeroBalance = request()->type == "Zero balance" ? true: false;

        if(auth()->user()->is_admin)
        {
            $customers = Customer::with('branch')->get();
        }
        else
        {
            $branch = auth()->user()->current;
            $customers = $branch->customers()->with('branch')->get();
        }

        $result = collect();
        foreach($customers as $customer)
        {
            $invoices = Invoice::with(['customer','payment'])
            ->active()
            ->where([
                    ['customer_id', $customer->id],
                    ['created_at', '>=', $date_from],
                    ['created_at', '<=', $date_to->addDays(1)->toDateString()],
                    ])
            ->orderby('created_at')
            ->get();

            $zerobalance = true;
            foreach($invoices as $invoice)
            {
                if($zerobalance){
                    $outstanding = $invoice->total - $invoice->payment->sum('total') - min($invoice->paid, $invoice->total);

                    if($outstanding > 0){
                        $zerobalance = false;
                    }
                }
            }

            if( $isOutstanding && !$zerobalance )
            {
                $result->push($customer);
            }
            else if( $isZeroBalance && $zerobalance )
            {
                $result->push($customer);
            }
        }

        return datatables()->of($result)
                            ->addColumn('group_name', function($customer){
                                return $customer->group ? $customer->group->name : "---";
                            })
                            ->toJson();
 
    }
}
