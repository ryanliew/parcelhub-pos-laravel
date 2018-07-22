<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Customer;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

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

        return json_encode(['message' => "Customer created" ]);
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

    public function statement(Customer $customer)
    {

        $this->validate_input_statement();

        $html = View::make('customer.statement', ["customer" => $customer])->render();

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

    public function view($customer_id, $start, $end)
    {

        $url = 'statements\soa_'. $customer_id . $start . $end . '.pdf';

        $path = storage_path($url );

        return response()->file($path);
    }


    public function report(Customer $customer)
    {
        $html = View::make('customer.statement', ["customer" => $customer])->render();

        $newPDF = new mPDF(['format' => 'Legal']);
        $newPDF->WriteHTML($html);
        $newPDF->setFooter('{PAGENO}/{nbpg}');

        $path = storage_path('statements\soa_' . $customer->id . '.pdf' );
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }

}
