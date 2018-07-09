<?php

namespace App\Http\Controllers;

use App\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function validate_input()
    {
        request()->validate([
            "invoice_no" => "required",
            "payment_method" => "required",
            "total" => "required",
            "branch_id" => "required",
            "terminal_no" => "required",
        ]);
    }

    
    public function page()
    {
        return view('payment.overview');
    }


    public function index()
    {
        return datatables()->of(Payment::all())->toJson();   
    }


    public function store()
    {
        //$this->validate_input();

       // $branch = Payment::create(request()->all());

        return json_encode(['message' => "New branch created. User  has been assigned to the branch."]);
    }
}
