<?php

namespace App\Http\Controllers;

use App\Item;
use App\Session;
use App\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class SessionController extends Controller
{
    public function receipt(Session $session)
    {
    	$items = Item::whereIn("invoice_id", $session->invoices->pluck('id'))->get();

    	// return view('invoice.receipt', ["invoice" => $invoice]);
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $html = View::make('session.receipt', ["session" => $session, "taxes" => Tax::all(), "items" => $items])->render();
        
        $mPDF = new mPDF(array('utf-8', array(72, 1000), 5, 'freesans', 2, 2, 2, 0, 0, 0, 'P', 
                        "fontDir" => array_merge($fontDirs, [storage_path('fonts/')]),
                        "fontdata" => $fontData + [
                            'monaco' => [
                                'R' => 'monaco.ttf'
                            ]
                        ],
                        'defaul_font' => 'monaco' ));

        $p = 'P';
        $mPDF->_setPageSize(array(72, 1000), $p);
        $mPDF->WriteHTML($html);
        $pageHeight = $mPDF->y + 5;
        // dd($pageHeight);
        $mPDF->page   = 0;
        $mPDF->state  = 0;
        unset($mPDF->pages[0]);

        $newPDF = new mPDF(array('utf-8', array(72, 1000), 5, 'freesans', 2, 2, 2, 0, 0, 0, 'P', 
                        "fontDir" => array_merge($fontDirs, [storage_path('fonts/')]),
                        "fontdata" => $fontData + [
                            'monaco' => [
                                'R' => 'monaco.ttf'
                            ]
                        ],
                        'defaul_font' => 'monaco' ));

        $newPDF->_setPageSize(array(72, $pageHeight), $p);
        $newPDF->WriteHTML($html);

        $path = storage_path('receipts\receipt_' . $session->id . '.pdf');
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }

    public function check(Session $session)
    {
        $items = Item::whereIn("invoice_id", $session->invoices->pluck('id'))->get();

        // return view('invoice.receipt', ["invoice" => $invoice]);
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $html = View::make('session.check', ["session" => $session, "taxes" => Tax::all(), "items" => $items])->render();
        
        $mPDF = new mPDF(array('utf-8', array(72, 1000), 5, 'freesans', 2, 2, 2, 0, 0, 0, 'P', 
                        "fontDir" => array_merge($fontDirs, [storage_path('fonts/')]),
                        "fontdata" => $fontData + [
                            'monaco' => [
                                'R' => 'monaco.ttf'
                            ]
                        ],
                        'defaul_font' => 'monaco' ));

        $p = 'P';
        $mPDF->_setPageSize(array(72, 1000), $p);
        $mPDF->WriteHTML($html);
        $pageHeight = $mPDF->y + 5;
        // dd($pageHeight);
        $mPDF->page   = 0;
        $mPDF->state  = 0;
        unset($mPDF->pages[0]);

        $newPDF = new mPDF(array('utf-8', array(72, 1000), 5, 'freesans', 2, 2, 2, 0, 0, 0, 'P', 
                        "fontDir" => array_merge($fontDirs, [storage_path('fonts/')]),
                        "fontdata" => $fontData + [
                            'monaco' => [
                                'R' => 'monaco.ttf'
                            ]
                        ],
                        'defaul_font' => 'monaco' ));

        $newPDF->_setPageSize(array(72, $pageHeight), $p);
        $newPDF->WriteHTML($html);

        $path = storage_path('checks\check_' . $session->id . '.pdf');
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }

    public function page()
    {
        return view("session.overview");
    }

    public function index()
    {
        $query = Session::select([
                            'sessions.*',
                            DB::raw('COUNT(invoices.id) as invoice_count'),
                        ])
                        ->rightJoin('invoices', 'invoices.session_id', '=', 'sessions.id')
                        ->groupBy('invoices.session_id')
                        ->having('invoice_count', '>', 0)
                        ->with('table', 'invoices');

        return datatables()->of($query)
                            ->addColumn('table_name', function(Session $session){
                                return $session->table->name;
                            })
                            ->toJson();
    }    

    public function view(Session $session)
    {
          return view("session.view", ['session' => $session->load('invoices.items', 'table')]);
    }  
}
