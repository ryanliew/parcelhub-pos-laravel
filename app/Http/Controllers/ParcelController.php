<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use App\Branch;
use App\ProductType;
use App\ParcelIntegrate;

class ParcelController extends Controller
{
    public function pageCheckInParcels()
	{
		return view('parcels.checkin');
    }
    
    public function checkInParcels() {
        $this->validate_input();           
        
        $client = new \GuzzleHttp\Client();
      
        $tracking_code = request()->tracking_code;
        $process_by_name = auth()->user()->name;
        $process_by_email = auth()->user()->email;
        $phone_number = request()->phone;
        $branch = auth()->user()->current->code;

        $response = $client->request('POST', env('VIRTUAL_MAILBOX_URI').'/api/parcel/checkin', [
        'headers' => [
            'Authorization' => "Bearer " . env('VMB_USER_TOKEN'),
        ], 
        'json' => ['tracking_code' => $tracking_code, 
                    'process_by_name' => $process_by_name, 
                    'process_by_email' => $process_by_email, 
                    'phone_number' => $phone_number, 
                    'branch_code' => $branch]
        ]);

      
        $response->getStatusCode(); // 200
        // $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
        $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'
        // $promise->wait();

        $data = json_decode( $response->getBody()->getContents() );

        return json_encode(['message' => $data->message]);        
    }

    public function index()
    {
        $client = new \GuzzleHttp\Client();

        $branch_code = auth()->user()->current->code;

        $item = Branch::all();
        $response = $client->request('GET', env('VIRTUAL_MAILBOX_URI').'/api/data/parcels', [
        'headers' => [
            'Authorization' => "Bearer " . env('VMB_USER_TOKEN'),
        ], 
        'json' => ['branch_code' => $branch_code]
        ]);

        $response->getStatusCode(); 
        $response->getBody();
        $data = json_decode( $response->getBody()->getContents() );     

        $parcels_detail = collect([]);
        if($data)
        {
            foreach($data->parcels as $parcel)
            {
                $detail = ['tracking_code' => $parcel->tracking_code, 
                            'phone' => $parcel->phone,
                            'status' => $parcel->status,
                            'charge' => $parcel->charge];      
                $parcels_detail->push($detail);
            }
            $data = (object) [];
            $prop = 'data';
            $data->{$prop} = $parcels_detail;
        }
        
        return json_encode($data);
    }
    
    public function validate_input()
	{
		request()->validate([
			"tracking_code" => 'required',
            "phone" => "required|regex:/^(01)[0-46-9]*[0-9]{7,8}$/",
        ]);
    }   

    public function parcelCharge() 
    {           
        $parcel = new ParcelIntegrate;
        $vmb_items = $parcel->getParcelItems(request()->items);

        $client = new \GuzzleHttp\Client();
    
        $response = $client->request('POST', env('VIRTUAL_MAILBOX_URI').'/api/parcels/charge', [
        'headers' => [
            'Authorization' => "Bearer " . env('VMB_USER_TOKEN'),
        ], 
        'json' => ['items' => $vmb_items, 
                    'pos_user_email' => auth()->user()->email,                     
                    'pos_user_branch_code' => auth()->user()->current->code]
        ]);
      
        $response->getStatusCode(); 
        $response->getBody();
        $data = json_decode( $response->getBody()->getContents() );

        return json_encode(['message' => $data->message, "return_items" => $data->return_items]);        
    }

    public function validateItems()
    {
        $parcel = new ParcelIntegrate;
        $vmb_items = $parcel->getParcelItems(request()->items);
        $cancel_reset = false;

        $client = new \GuzzleHttp\Client();
    
        $response = $client->request('POST', env('VIRTUAL_MAILBOX_URI').'/api/parcels/charge/validate', [
        'headers' => [
            'Authorization' => "Bearer " . env('VMB_USER_TOKEN'),
        ], 
        'json' => ['items' => $vmb_items, 
                    'pos_user_email' => auth()->user()->email,                     
                    'pos_user_branch_code' => auth()->user()->current->code ]
        ]);
      
        $response->getStatusCode(); 
        $response->getBody();
        $data = json_decode( $response->getBody()->getContents() ); 

        $cancel_reset = false;  // do not reset the data for validation    

        return json_encode(['message' => $data->message, 'is_valid' => $data->is_valid, 'cancel_reset' => $cancel_reset]);  
    }
}