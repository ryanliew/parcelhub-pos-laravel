<?php

namespace App\Http\Controllers;

use App\Dropoff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class DropoffController extends Controller
{
    public function page()
    {
        return view("dropoff.overview");
    }

    public function index()
    {
        $branch = auth()->user()->current;
        $query = $branch->dropoffs()->with(["customer", "vendor", "user", "items"])->select("dropoffs.*");

        return datatables()
            ->of($query)
            ->addColumn('customer', function(Dropoff $dropoff){
                return $dropoff->customer->name;
            })
            ->addColumn('vendor', function(Dropoff $dropoff){
                return $dropoff->vendor->name;
            })
            ->toJson();
    }

    public function create()
    {
        return view("dropoff.create");
    }

    public function store()
    {
        request()->validate([
            "customer_id" => "required|exists:customers,id"
        ]);

        $user = auth()->user();

        $dropoff_no = $user->current->code . "D" . sprintf("%05d", ++$user->current->sequence->dropoff_sequence);

        $dropoff = Dropoff::create([
            "customer_id" => request()->customer_id,
            "branch_id" => $user->current_branch,
            "terminal_id" => $user->current_terminal,
            "status" => Dropoff::STATUS_NEW,
            "vendor_id" => request()->vendor_id,
            "user_id" => $user->id,
            "remarks" => request()->remarks,
            "type" => Dropoff::TYPE_DROPOFF,
            "dropoff_no" => $dropoff_no,
        ]);

        $items = collect(json_decode(request()->barcodes));

        $items = $items->map(function($code){
            return ["consignment_note" => $code];
        });

        $user->current->sequence()->update(["dropoff_sequence" => $user->current->sequence->dropoff_sequence]);

        $dropoff->items()->createMany($items->all());

        return [
            "dropoff" => $dropoff,
            "message" => "Dropoff " . $dropoff->dropoff_no . " created.",
            "redirect_url" => route("dropoff.receipt", ["dropoff" => $dropoff->id]),
            "admin_redirect_url" => route("dropoff.admin", ["dropoff" => $dropoff->id]),
        ];
    }

    public function view(Dropoff $dropoff)
    {
        return view("dropoff.pickup")
            ->with("dropoff", $dropoff->load(["vendor", "items"]));
    }

    public function pickedUp(Dropoff $dropoff)
    {
        $dropoff->update([
            "picked_up_on" => now(),
            "picked_up_by" => request()->picked_up_by,
            "vehicle_no" => request()->vehicle_no,
            "status" => Dropoff::STATUS_COMPLETE,
        ]);

        return ["dropoff" => $dropoff, "message" => "Pickup success"];
    }

    public function update(Dropoff $dropoff)
    {
        $dropoff->update([
            "picked_up_on" => request()->picked_up_on ?? now(),
            "picked_up_by" => request()->picked_up_by,
            "vehicle_no" => request()->vehicle_no,
            "status" => Dropoff::STATUS_COMPLETE,
        ]);

        return ["dropoff" => $dropoff, "message" => "Pickup updated successfully"];
    }

    public function pickup(Dropoff $dropoff)
    {
        return view("dropoff.pickup")
                    ->with("dropoff", $dropoff->load(["vendor", "items"]));
    }

    public function receipt(Dropoff $dropoff)
    {
        $html = View::make("dropoff.preview", ["dropoff" => $dropoff])->render();

        $newPDF = new mPDF(['format' => 'Legal']);
        $newPDF->WriteHTML($html);
        $newPDF->setFooter('{PAGENO}/{nbpg}');

        $path = storage_path('receipts\dropoff_' . $dropoff->dropoff_no . '.pdf');
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }

    public function admin(Dropoff $dropoff)
    {
        $html = View::make("dropoff.preview", ["dropoff" => $dropoff, "admin" => true])->render();

        $newPDF = new mPDF(['format' => 'Legal']);
        $newPDF->WriteHTML($html);
        $newPDF->setFooter('{PAGENO}/{nbpg}');

        $path = storage_path('receipts\dropoff_' . $dropoff->dropoff_no . '.pdf');
        $newPDF->Output($path, Destination::FILE);

        return response()->file($path);
    }
}
