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

    public function store()
    {
    	$terminal = auth()->user()->terminal;

    	$invoices = $terminal->invoices()->cashupRequired()->latest()->get();

    	if($invoices->count() > 0)
    	{
	    	$cashup = $terminal->cashups()->create([
	    		'invoice_from' => $invoices->last()->invoice_no,
	    		'invoice_to' => $invoices->first()->invoice_no,
	    		'total' => $invoices->sum(function($invoice){ return $invoice->total; }) + $terminal->float,
	    		'session_start' => $invoices->last()->created_at,
	    		'created_by' => auth()->id(),
	    		'branch_id' => $terminal->branch_id
	    	]);

	    	$terminal->invoices()->cashupRequired()->latest()->update(['cashed' => true, 'cashup_id' => $cashup->id]);

	    	return json_encode(['message' => "Cash up report generated"]);
	    }

	    return json_encode(['message' => "No invoices pending for cash up"]);
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
