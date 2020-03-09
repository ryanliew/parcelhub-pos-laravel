<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use App\Branch;

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

        $response = $client->request('POST', env('VIRTUAL_MAILBOX_URI').'/api/data/pos/parcels', [
        'headers' => [
            'Authorization' => env('VMB_USER_TOKEN'),
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
            'Authorization' => env('VMB_USER_TOKEN'),
        ], 
        'json' => ['branch_code' => $branch_code]
        ]);

        $response->getStatusCode(); 
        $response->getBody();
        $data = json_decode( $response->getBody()->getContents() );     

        $parcels_detail = collect([]);
        foreach($data->parcels as $parcel)
        {
            $detail = ['tracking_code' => $parcel->tracking_code, 
                        'phone' => $parcel->phone,
                        'status' => $parcel->status,
                        'charge' => $parcel->charge];      
            $parcels_detail->push($detail);
        }
        //dd(json_encode($parcels_detail));


        $data = (object) [];
        $prop = 'data';
        $data->{$prop} = $parcels_detail;


        // $data = (object);
        // $data->createProperty('data', json_encode($parcels_detail));
       // $data = (object)json_encode($parcels_detail);
        //dd($data);
        // $data = json_encode($parcels_detail); 
        // dd($data);
        return json_encode($data);



		// $user = auth()->user();
		// $query = $user->profit_and_losses()->with([])->select('profit_and_losses.*');

        // return datatables()->of($query)
        //                     ->addColumn('tracking_code', function($parcel){
        //                         return $parcel->tracking_code;
		// 					})							
		// 					->addColumn('phone', function($profit_and_loss){
        //                         return $parcel->phone;
        //                     })
        //                     ->addColumn('status', function($profit_and_loss){
        //                         return $parcel->status;
        //                     })
        //                     ->addColumn('charge', function($profit_and_loss){
        //                         return $parcel->charge; //number_format((float) $parcel->charge ,2,'.','')
        //                     })
        //                     ->toJson();
    }
    
    public function validate_input()
	{
		request()->validate([
			"tracking_code" => 'required',
            "phone" => "required|regex:/^(01)[0-46-9]*[0-9]{7,8}$/",
        ]);
    }   
}