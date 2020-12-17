<?php

namespace App\Http\Controllers;

use App\Cashup;
use App\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class CashupController extends Controller
{
	public function page()
    {
    	return view('cashup.overview');
    }

    public function index()
    {
        $terminal = auth()->user()->terminal;

    	return datatables()
			->of($terminal->cashups()->with('terminal')->where('status', "!=", "canceled")->latest())
			->addColumn('terminal_name', function(Cashup $cashup) {
				return $cashup->terminal->name;
			})
    		->toJson();   
    }

    public function view(Cashup $cashup)
    {
        return view('cashup.view', ['cashup' => $cashup->load('invoices', 'terminal', 'creator', 'details')]);
    }

    public function store()
    {
    	$terminal = auth()->user()->terminal;

    	$invoices = $terminal->invoices()->cashupRequired()->active()->latest()->get();

        $payments = $terminal->payments()->cashupRequired()->with('payments.invoice')->latest()->get();

    	if($invoices->count() > 0 || $payments->count() > 0)
    	{

            $last_id = $invoices->last()->invoice_no;
            $first_id = $invoices->first()->invoice_no;
            
            // No longer needed
            // use mindate(invoice date, payment date)
            // $earliest_invoice_date = $invoices->count() > 0 ? $invoices->last()->created_at : $payments->last()->created_at;
            // $earliest_payment_date = $payments->count() > 0 ? $payments->last()->created_at : $invoices->last()->created_at;
            // $session_start = $earliest_invoice_date < $earliest_payment_date ? $earliest_invoice_date : $earliest_payment_date;

            //$session_start = $invoices->count() > 0 ? $invoices->last()->created_at : $payments->last()->created_at;

            // set previous draft to canceled
            $lastCashup = $terminal->cashups()->where('status','draft')->get()->last();
            if($lastCashup){
                $lastCashup->update([
                    'status' => 'canceled'
                ]);
            }            

	    	$cashup = $terminal->cashups()->create([

	    		'invoice_from' => $last_id,
	    		'invoice_to' => $first_id,
	    		'total' => $invoices->sum(function($invoice){ return $invoice->total; }) + $terminal->float,
	    		'session_start' => now(),
	    		'created_by' => auth()->id(),
	    		'branch_id' => $terminal->branch_id,
                'float_value' => $terminal->float
	    	]);            
            
            if($invoices->count() > 0) {

                $cashup->invoices()->attach($this->formatInvoices($invoices));

                // $terminal->invoices()->cashupRequired()->latest()->update(['cashed' => true]);
            }

            if($payments->count() > 0) {
                
                $payments = $this->formatAndAttachPayments($payments, $cashup);

                // To cater for multiple same payment for the same invoice within this cashup
                // $cashup->invoices()->attach($this->formatPayments($payments));

                // $terminal->payments()->cashupRequired()->latest()->update(['cashed' => true, 'cashup_id' => $cashup->id]);
            }

            $invoices = $cashup->invoices()->orderBy('invoice_no')->get();

            $cashup->update([
                'invoice_from' => $invoices->first()->invoice_no,
                'invoice_to' => $invoices->last()->invoice_no,
                'total' => $invoices->sum(function($invoice){ return $invoice->pivot->total; }) + $terminal->float
            ]);

            // Create cashup details before hand
            foreach($invoices->groupBy(function($item){ return $item->pivot->payment_method; }) as $type => $records) {
                $amount = $records->sum(function($invoice){ return $invoice->pivot->total; });
                $legend = "00";

                switch($type) {
                    case 'IBG':
                        $legend = "17";
                        break;
                    case 'Cash':
                        $legend = "01";
                        break;
                    case 'Credit card':
                        $legend = "02";
                        break;
                    case "Cheque":
                        $legend = "05";
                        break;
                }

                $cashup->details()->create([
                    'expected_amount' => $amount,
                    'actual_amount' => $amount,
                    'legend' => $legend,
                    'type' => $type,
                    'percentage' => $amount / $cashup->total * 100,
                    'count' => $records->count()
                ]);
            }

            if($cashup->total > 0)
                $cashup->details()->create([
                    'expected_amount' => $cashup->float_value,
                    'actual_amount' => $cashup->float_value,
                    'legend' => "00",
                    'type' => "Float",
                    'percentage' => $cashup->float_value / $cashup->total * 100,
                ]);

	    	return json_encode(['message' => "Cash up report generated", "id" => $cashup->id]);
	    }

	    return json_encode(['message' => "No invoices pending for cash up"]);
    }

    public function update(Cashup $cashup)
    {
        $actuals = collect(json_decode(request()->actuals));

        foreach($cashup->details as $detail){
            $actual = $actuals->firstWhere('type', $detail->type) ? $actuals->firstWhere('type', $detail->type)->actual_amount : 0.00;

            $detail->update([
                'actual_amount' => $actual
            ]);
        }
        
        $cashup->update([
            'actual_amount' => request()->actual_amount,
            'status' => 'confirmed'
        ]);

        // Should only update invoices and payments to cashed that belongs to this cashup
        $invoices = $cashup->invoices->pluck("id");
        $payments = $cashup->payments->pluck("id");

        $terminal = auth()->user()->terminal;
        $terminal->invoices()->whereIn("id", $invoices)->update(['cashed' => true]);
        $terminal->payments()->whereIn("id", $payments)->update(['cashed' => true, 'cashup_id' => $cashup->id]);


    	// $invoices = $terminal->invoices()->cashupRequired()->active()->latest()->get();
     //    $payments = $terminal->payments()->cashupRequired()->with('payments.invoice')->latest()->get();
     //    if($invoices->count() > 0) {
     //        $terminal->invoices()->cashupRequired()->latest()->update(['cashed' => true]);
     //    }

     //    if($payments->count() > 0) {                
     //        $terminal->payments()->cashupRequired()->latest()->update(['cashed' => true, 'cashup_id' => $cashup->id]);
     //    }

        return json_encode(['message' => "Cashup complete"]);
    }

    public function delete(Cashup $cashup)
    {
        $cashup->payments()->update([
            'cashed' => false,
            'cashup_id' => null
        ]);

        foreach($cashup->invoices as $invoice) {
            $invoice->update(['cashed' => false]);
        }

        $cashup->invoices()->detach();

        $cashup->delete();

        return json_encode(['message' => "Draft cashup deleted. Redirecting."]);
    }

    public function formatInvoices($invoices)
    {
        $arr = [];

        foreach($invoices as $invoice) {
            $arr[$invoice->id] = ['total' => $invoice->total, 
            'payment_method' => $invoice->payment_type . ( $invoice->type != 'Cash'?  " - " . $invoice->type : "" ),
            'payment_id' => $invoice->payment_id ?: 0];
        }

        return $arr;
    }

    public function formatAndAttachPayments($payments, $cashup)
    {   
        // $invoices = collect();

        foreach($payments as $payment) {
            foreach($payment->payments as $payment_invoice) {
                $payment_invoice->invoice->total = $payment_invoice->total;
                $payment_invoice->invoice->paid = $payment_invoice->total;
                $payment_invoice->invoice->payment_type = $payment->payment_method;
                $payment_invoice->invoice->payment_id = $payment->id;

                $invoice = $payment_invoice->invoice;
                // $invoices->push($payment_invoice->invoice);

                $cashup->invoices()
                        ->attach($invoice->id, [
                            'total' => $invoice->total, 
                            'payment_method' => $invoice->payment_type . ( $invoice->type != 'Cash'?  " - " . $invoice->type : "" ),
                            'payment_id' => $invoice->payment_id
                        ]);
            }
        }

        // To cater for multiple same payment for the same invoice within this cashup
        return $cashup;
    }

    public function report(Cashup $cashup)
    {
    	$html = View::make('cashup.report', ["cashup" => $cashup, "invoices" => $cashup->invoices])->render();

        $newPDF = new mPDF(['tempDir' => storage_path('mpdf'), 'format' => 'Legal']);
        $newPDF->WriteHTML($html);
        $newPDF->setFooter('{PAGENO}/{nbpg}');

        $path = storage_path('cashups\cashup_' . $cashup->id . '.pdf');
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }
}
