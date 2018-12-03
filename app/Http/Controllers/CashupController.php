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
			->of($terminal->cashups()->with('terminal')->latest())
			->addColumn('terminal_name', function(Cashup $cashup) {
				return $cashup->terminal->name;
			})
    		->toJson();   
    }

    public function view(Cashup $cashup)
    {
        return view('cashup.view', ['cashup' => $cashup->load('invoices')]);
    }

    public function store()
    {
    	$terminal = auth()->user()->terminal;

    	$invoices = $terminal->invoices()->cashupRequired()->latest()->get();

        $payments = $terminal->payments()->cashupRequired()->with('payments.invoice')->latest()->get();

    	if($invoices->count() > 0 || $payments->count() > 0)
    	{

            $last_id = $invoices->last()->invoice_no;
            $first_id = $invoices->first()->invoice_no;
            $session_start = $invoices->count() > 0 ? $invoices->last()->created_at : $payments->last()->created_at;

	    	$cashup = $terminal->cashups()->create([

	    		'invoice_from' => $last_id,
	    		'invoice_to' => $first_id,
	    		'total' => $invoices->sum(function($invoice){ return $invoice->total; }) + $terminal->float,
	    		'session_start' => $session_start,
	    		'created_by' => auth()->id(),
	    		'branch_id' => $terminal->branch_id,
                'float_value' => $terminal->float
	    	]);

            if($invoices->count() > 0) {

                $cashup->invoices()->attach($this->formatInvoices($invoices));

    	    	$terminal->invoices()->cashupRequired()->latest()->update(['cashed' => true]);
            }

            if($payments->count() > 0) {
                
                $cashup->invoices()->attach($this->formatPayments($payments));

                $terminal->payments()->cashupRequired()->latest()->update(['cashed' => true, 'cashup_id' => $cashup->id]);

            }

            $invoices = $cashup->invoices()->orderBy('invoice_no')->get();

            $cashup->update([
                'invoice_from' => $invoices->first()->invoice_no,
                'invoice_to' => $invoices->last()->invoice_no,
                'total' => $invoices->sum(function($invoice){ return $invoice->pivot->total; }) + $terminal->float
            ]);

	    	return json_encode(['message' => "Cash up report generated", "id" => $cashup->id]);
	    }

	    return json_encode(['message' => "No invoices pending for cash up"]);
    }

    public function update(Cashup $cashup)
    {
        $cashup->update([
            'actual_amount' => request()->actual_amount,
            'status' => 'confirmed'
        ]);

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
            $arr[$invoice->id] = ['total' => $invoice->total <= $invoice->paid ? $invoice->total : $invoice->paid, 'payment_method' => $invoice->payment_type, 'payment_id' => $invoice->payment_id ?: 0];
        }

        return $arr;
    }

    public function formatPayments($payments)
    {   
        $invoices = collect();

        foreach($payments as $payment) {
            foreach($payment->payments as $payment_invoice) {
                $payment_invoice->invoice->total = $payment_invoice->total;
                $payment_invoice->invoice->paid = $payment_invoice->total;
                $payment_invoice->invoice->payment_type = $payment->payment_method;
                $payment_invoice->invoice->payment_id = $payment->id;
                $invoices->push($payment_invoice->invoice);
            }
        }

        return $this->formatInvoices($invoices);

    }

    public function report(Cashup $cashup)
    {
    	$html = View::make('cashup.report', ["cashup" => $cashup, "invoices" => $cashup->invoices])->render();

        $newPDF = new mPDF(['format' => 'Legal']);
        $newPDF->WriteHTML($html);
        $newPDF->setFooter('{PAGENO}/{nbpg}');

        $path = storage_path('cashups\cashup_' . $cashup->id . '.pdf');
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }
}
