<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Invoice;
use App\Item;
use App\Payment;
use App\PaymentInvoice;
use App\Product;
use App\Tax;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class InvoiceController extends Controller
{
    public function page()
    {
    	return view('invoice.overview');
    }

    public function page_canceled()
    {
        return view('invoice.canceled');
    }

    public function create()
    {
        return view('invoice.create');
    }

    public function edit(Invoice $invoice)
    {
        return view('invoice.edit', ["invoice" => $invoice]);
    }

    public function get($invoice)
    {
        $result = Invoice::with(['customer', 'items'])->find($invoice);

        return $result;
    }

    public function index()
    {
        // Improve tracking number search performance by searching from items table directly then incorporate the invoice id to the invoices search
        $terminal = auth()->user()->terminal()->first();
        $query = $terminal->invoices()->with(['customer','payment', 'branch', 'terminal', 'items'])->select('invoices.*')->active();

        $items = collect();

        if(request()['search']['value'])
            $items = Item::where('tracking_code', 'like', '%' . request()['search']['value'] . '%')
                    ->where('branch_id', $terminal->branch->id)
                    ->join('invoices', 'invoice_id' , '=' , 'invoices.id')
                    ->get();

    	return datatables()
			->of($query)
                ->filter(function($query) use ($items){
                    if($items->count() > 0) {
                        $query->orWhere(function($query) use ($items) {
                            $query->whereIn('invoices.id', $items->pluck('invoice_id'));
                        });
                    }

                    if(request()['search']['value'])
                        $query->orWhere(function($query) use ($items) {
                            $query->where('type', 'like', '%' . request()['search']['value'] . '%')
                                  ->where('terminal_no', auth()->user()->current_terminal);
                        });
                }, true)
    			->addColumn('payment', function(Invoice $invoice) {
                    return $invoice->payment->sum('total') + $invoice->paid;
                })
    			->addColumn('outstanding', function(Invoice $invoice) {
                    $outstanding = $invoice->total  - $invoice->payment->sum('total') - $invoice->paid;
                    
                    return $invoice->total < 0 ? 
                            $outstanding : 
                            max($outstanding, 0);
                })
                ->addColumn('customer', function(Invoice $invoice){ 
                    return $invoice->customer ? $invoice->customer->name : "Cash";
                })
                ->addColumn('tracking_codes', function(Invoice $invoice){
                    return $invoice->items->implode('tracking_code', ', ');
                })
    			->toJson();   
    }

    public function index_payment()
    {
        // Improve tracking number search performance by searching from items table directly then incorporate the invoice id to the invoices search
        $terminal = auth()->user()->terminal()->first();

        $query = $terminal->invoices()->with(['customer','payment', 'branch', 'terminal', 'items'])
                        ->whereBetween('created_at', [request()->start, request()->end])
                        ->where('customer_id', request()->customer)
                        ->select('invoices.*')->active();

        $items = collect();

        if(request()['search']['value'])
            $items = Item::where('tracking_code', 'like', '%' . request()['search']['value'] . '%')
                    ->where('branch_id', $terminal->branch->id)
                    ->join('invoices', 'invoice_id' , '=' , 'invoices.id')
                    ->get();

        return datatables()
            ->of($query)
                ->filter(function($query) use ($items){
                    if($items->count() > 0) {
                        $query->orWhere(function($query) use ($items) {
                            $query->whereIn('invoices.id', $items->pluck('invoice_id'));
                        });
                    }

                    if(request()['search']['value'])
                        $query->orWhere(function($query) use ($items) {
                            $query->where('type', 'like', '%' . request()['search']['value'] . '%')
                                  ->where('terminal_no', auth()->user()->current_terminal);
                        });
                }, true)
                ->addColumn('payment', function(Invoice $invoice) {
                    return $invoice->payment->sum('total') + $invoice->paid;
                })
                ->addColumn('outstanding', function(Invoice $invoice) {
                    $outstanding = $invoice->total  - $invoice->payment->sum('total') - $invoice->paid;
                    
                    return $invoice->total < 0 ? 
                            $outstanding : 
                            max($outstanding, 0);
                })
                ->addColumn('customer', function(Invoice $invoice){ 
                    return $invoice->customer ? $invoice->customer->name : "Cash";
                })
                ->addColumn('tracking_codes', function(Invoice $invoice){
                    return $invoice->items->implode('tracking_code', ', ');
                })
                ->toJson();   
    }

    public function index_canceled()
    {
        // Improve tracking number search performance by searching from items table directly then incorporate the invoice id to the invoices search
        $terminal = auth()->user()->terminal()->first();
        $query = $terminal->invoices()->with(['customer','payment', 'branch', 'terminal', 'items'])->select('invoices.*')->canceled();

        $items = collect();

        if(request()['search']['value'])
            $items = Item::where('tracking_code', 'like', '%' . request()['search']['value'] . '%')
                    ->where('branch_id', $terminal->branch->id)
                    ->join('invoices', 'invoice_id' , '=' , 'invoices.id')
                    ->get();

        return datatables()
            ->of($query)
                ->filter(function($query) use ($items){
                    if($items->count() > 0) {
                        $query->orWhere(function($query) use ($items) {
                            $query->whereIn('invoices.id', $items->pluck('invoice_id'));
                        });
                    }

                    if(request()['search']['value'])
                        $query->orWhere(function($query) use ($items) {
                            $query->where('type', 'like', '%' . request()['search']['value'] . '%')
                                  ->where('terminal_no', auth()->user()->current_terminal);
                        });
                }, true)
                ->addColumn('customer', function(Invoice $invoice){ 
                    return $invoice->customer ? $invoice->customer->name : "Cash";
                })
                ->addColumn('tracking_codes', function(Invoice $invoice){
                    return $invoice->items->implode('tracking_code', ', ');
                })
                ->toJson();   
    }

    public function validateInput()
    {
        
    }

    public function store()
    {
        $items = json_decode(request()->items);

        // dd($items);
        Log::info("Creating invoice by:" . auth()->user()->name);
        Log::info(request()->all());
        Log::info($items);

        $repeating_trackings = collect();

        foreach($items as $item)
        {
            // We need to make sure that empty tracking codes don't get checked

            $code = trim($item->tracking_code);

            $repeating = Item::where('tracking_code', $code)
                            ->join('invoices', 'invoice_id' , '=' , 'invoices.id')
                            ->where('invoices.branch_id', auth()->user()->current->id)
                            ->count() > 0;

            if($repeating && !empty($code)) {
                $repeating_trackings->push($item->tracking_code);
            }
        }

        if($repeating_trackings->count() > 0) {
            $error = "These tracking number already exists: " . $repeating_trackings->implode(', ');
           
            return $this->returnValidationErrorResponse([['something' => 'something']], $error);
        }

        $user = User::find(request()->created_by);

        $branch = auth()->user()->current;

        $invoice_no = $branch->code . sprintf("%05d", ++$branch->sequence->last_id);

        $invoice = Invoice::create([
            'subtotal' => request()->has('subtotal') ? request()->subtotal : 0.00,
            'total' =>  request()->has('total') ? request()->total : 0.00,
            'tax' => request()->has('tax') ? request()->tax : 0.00,
            'paid' => request()->has('paid') ? request()->paid : 0.00,
            'type' => request()->type,
            'payment_type' => request()->payment_type,
            'branch_id' => $user->current_branch,
            'terminal_no' => $user->current_terminal,
            'created_by' => $user->id,
            'discount_value' => request()->has('discount_value') ? request()->discount_value : 0.00,
            'discount_mode' => request()->discount_mode,
            'discount' => request()->has('discount') ? request()->discount : 0.00,
            'remarks' => request()->remarks,
            'customer_id' => request()->customer_id,
            'invoice_no' => $invoice_no,
        ]);

        $branch->sequence()->update(["last_id" => $branch->sequence->last_id]);

        foreach($items as $item)
        { 
            $product = Product::find($item->product_id);
            
            $invoice->items()->create([
                'tracking_code' => $item->tracking_code,
                'description' => $item->description,
                'zone' => $item->zone,
                'weight' => $item->weight,
                'dimension_weight' => $item->dimension_weight,
                'height' => isset($item->height) ? $item->height : 0,
                'length' => isset($item->length) ? $item->length : 0,
                'width' => isset($item->width) ? $item->width: 0,
                'sku' => $item->sku,
                'tax' => $item->tax == 'NaN' ? 0.00 : $item->tax, // To counter sometimes taxes will become "NaN" and causes failure
                'price' => $item->price,
                'courier_id' => isset($item->courier_id) ? $item->courier_id : 0,
                'product_id' => $item->product_id,
                'product_type_id' => $item->product_type_id,
                'total_price' => $item->total_price,
                'unit' => $item->unit,
                'is_custom_pricing' => $item->is_custom_pricing,
                'tax_rate' => $item->tax_rate,
                'tax_type' => $item->tax_type,
                'zone_type_id' => empty($item->zone_type_id) ? $product->zone_type_id : $item->zone_type_id,
            ]);
        }

        $invoice_url = self::checkForCustomerPayment($invoice);
        //$invoice->items()->create($items);

        $url = $invoice->payment_type !== "Account" ? "/invoices/receipt/" . $invoice->id : "/invoices/preview/" . $invoice->id;

        return json_encode(['message' => "Invoice created successfully, redirecting to invoice list page", "id" => $invoice->id, "redirect_url" => $url, "invoice_url" => $invoice_url]);
    }

    public function checkForCustomerPayment($invoice)
    {
        $url = "";

        if($invoice->customer_id && $invoice->paid != 0) {
            Log::info("Creating payment by : " . auth()->user()->name);
            Log::info($invoice);

            $payment = Payment::create([
                'customer_id'   => $invoice->customer_id,
                'branch_id'     => $invoice->branch_id,
                'terminal_no'   => $invoice->terminal_no,
                'total'         =>  min($invoice->total, $invoice->paid),
                'payment_method' => $invoice->payment_type,
                'created_by'    => $invoice->created_by,
            ]);

            $receipt = PaymentInvoice::create([
                'payment_id'    => $payment->id,
                'invoice_no'    => $invoice->invoice_no,
                'total'         => $invoice->total,
                'invoice_total' => $invoice->total,
                'outstanding'   => max($invoice->paid - $invoice->total, 0),
                'paid'          => $invoice->paid
            ]);

            $invoice->paid = 0;
            $invoice->save();

            $url = "/payments/receipt/" . $payment->id;
        }

        return $url;
    }

    public function update(Invoice $invoice)
    {
        $items = json_decode(request()->items);

        // dd($items);

        $user = User::find(request()->created_by);

        $invoice->update([
            'subtotal' => request()->has('subtotal') ? request()->subtotal : 0.00,
            'total' =>  request()->has('total') ? request()->total : 0.00,
            'tax' => request()->has('tax') ? request()->tax : 0.00,
            'paid' => request()->has('paid') ? request()->paid : 0.00,
            'type' => request()->type,
            'payment_type' => request()->payment_type,
            'branch_id' => $user->current_branch,
            'terminal_no' => $user->current_terminal,
            'created_by' => $user->id,
            'discount_value' => request()->has('discount_value') ? request()->discount_value : 0.00,
            'discount_mode' => request()->discount_mode,
            'discount' => request()->has('discount') ? request()->discount : 0.00,
            'remarks' => request()->remarks,
            'customer_id' => request()->customer_id
        ]);

        $invoice->items()->delete();

        foreach($items as $item)
        {
            $invoice->items()->create([
                'tracking_code' => $item->tracking_code,
                'description' => $item->description,
                'zone' => $item->zone,
                'weight' => $item->weight,
                'dimension_weight' => $item->dimension_weight,
                'height' => isset($item->height) ? $item->height : 0,
                'length' => isset($item->length) ? $item->length : 0,
                'width' => isset($item->width) ? $item->width: 0,
                'sku' => $item->sku,
                'tax' => $item->tax,
                'price' => $item->price,
                'courier_id' => isset($item->courier_id) ? $item->courier_id : 0,
                'product_id' => $item->product_id,
                'product_type_id' => $item->product_type_id,
                'total_price' => $item->total_price,
                'unit' => $item->unit,
                'is_custom_pricing' => $item->is_custom_pricing,
                'zone_type_id' => $item->zone_type_id,
                'tax_rate' => $item->tax_rate,
                'tax_type' => $item->tax_type,
            ]);
        }
        //$invoice->items()->create($items);
        $url = $invoice->customer_id ? "/invoices/preview/" . $invoice->id : "/invoices/receipt/" . $invoice->id;
        
        return json_encode(['message' => "Invoice updated successfully, redirecting to invoice list page", "id" => $invoice->id, "redirect_url" => $url]);
    }

    public function receipt(Invoice $invoice)
    {
        // return view('invoice.receipt', ["invoice" => $invoice]);
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $html = View::make('invoice.receipt', ["invoice" => $invoice, "taxes" => Tax::all()])->render();
        
        $mPDF = new mPDF(array('tempDir' => storage_path('mpdf'), 'utf-8', array(80, 1000), 5, 'freesans', 2, 2, 2, 0, 0, 0, 'P', 
                        "fontDir" => array_merge($fontDirs, [storage_path('fonts/')]),
                        "fontdata" => $fontData + [
                            'monaco' => [
                                'R' => 'monaco.ttf'
                            ]
                        ],
                        'defaul_font' => 'monaco' ));

        $p = 'P';
        $mPDF->_setPageSize(array(80, 1000), $p);
        $mPDF->WriteHTML($html);
        $pageHeight = $mPDF->y + 5;
        // dd($pageHeight);
        $mPDF->page   = 0;
        $mPDF->state  = 0;
        unset($mPDF->pages[0]);

        $newPDF = new mPDF(array('tempDir' => storage_path('mpdf'), 'utf-8', array(80, 1000), 5, 'freesans', 2, 2, 2, 0, 0, 0, 'P', 
                        "fontDir" => array_merge($fontDirs, [storage_path('fonts/')]),
                        "fontdata" => $fontData + [
                            'monaco' => [
                                'R' => 'monaco.ttf'
                            ]
                        ],
                        'defaul_font' => 'monaco' ));
        $newPDF->_setPageSize(array(80, $pageHeight), $p);
        $newPDF->WriteHTML($html);

        $path = storage_path('receipts\receipt_' . $invoice->id . '.pdf');
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }

    public function delivery_order(Invoice $invoice)
    {
       return InvoiceController::generate_pdf('invoice.delivery', $invoice);
    }

    public function preview(Invoice $invoice)
    {
       return InvoiceController::generate_pdf('invoice.preview', $invoice);
    }

    public function generate_pdf($view, Invoice $invoice)
    {
        $html = View::make($view, ["invoice" => $invoice])->render();

        $newPDF = new mPDF(['format' => 'Legal']);
        $newPDF->WriteHTML($html);
        $newPDF->setFooter('{PAGENO}/{nbpg}');

        $path = storage_path('receipts\invoice_' . $invoice->id . '.pdf');
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }

    public function validateTracking()
    {
        $tracking = trim(request()->code);

        return ['result' => Item::where('tracking_code', $tracking)
                        ->join('invoices', 'invoice_id' , '=' , 'invoices.id')
                        ->where('invoices.branch_id', auth()->user()->current->id)
                        ->whereNull('invoices.canceled_on')
                        ->count() > 0];
    }

    public function validateCancel($password, $invoice)
    {
        $message = '';

        $admins = $invoice->branch->permissions()->with('user')->write()->get()->pluck('user');

        if(!$admins->contains(function($value, $key) use ($password){ return Hash::check($password, $value->password); })) 
            $message = "Incorrect password, you need an admin password";

        if($invoice->cashup()->count() > 0)
            $message = "Invoice already included in cash up";

        if($invoice->payment()->count() > 0)
            $message = "Invoice already received payment";

        return $message;
    }

    public function cancel(Invoice $invoice)
    {
        $message = $this->validateCancel(request()->password, $invoice);
        
        if(empty($message)) {
            $invoice->update([
                'canceled_by' => auth()->id(),
                'canceled_on' => Carbon::now()->toDateTimeString(),
                'remarks' => "Canceled - " . request()->remarks,
            ]);

            return json_encode(['message' => "Invoice canceled, reloading"]);
        }

        return json_encode(['error' => $message, 'message' => "Unable to cancel invoice"]);
    }

}
