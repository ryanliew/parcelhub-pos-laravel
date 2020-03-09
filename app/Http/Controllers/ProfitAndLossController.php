<?php

namespace App\Http\Controllers;
use App\Item;
use App\ProfitAndLoss;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel as Excel;

class ProfitAndLossController extends Controller
{
    //
    public function page()
	{
		return view('profitloss.view');
	}

	public function import()
	{
		request()->validate([
            "file" => "required"
        ]);

        $excelRows= Excel::load(request()->file('file'))->noHeading()->skipRows(1)->toArray();

        $profit_losses = collect([]);

        foreach($excelRows as $excelRow) {

            $detail = [];
            $count = 0;
            
            if(!is_null($excelRow[0])) {
                $item = Item::where('tracking_code', $excelRow[0])->first();

                // if(is_null($item) && !is_null($excelRow[0]) && $excelRow[0] !== "---")
                // {
                //     return $this->returnValidationErrorResponse(['file' => ['Tracking code ' . $excelRow[0] . ' not found.']]);
                // }

                $detail['tracking_code'] = $excelRow[0];
				$detail['sales'] = $excelRow[1];
                $profit_losses->push($detail);
            }
        }

		// delete all records from same user
		$profit_losses_old = ProfitAndLoss::where('created_by', request()->created_by);
		$profit_losses_old -> delete();

		// create new records from excel record
        foreach($profit_losses as $profit_loss)
        {
            ProfitAndLoss::updateOrCreate(["tracking_code" => $profit_loss["tracking_code"], "created_by" => request()->created_by],
                [
                    "tracking_code" => $profit_loss['tracking_code'],
					"sales" => $profit_loss['sales'],
					"created_by" => request()->created_by,
                ]
            );

            $count++;
        }

        return ["message" => "Processed " . $count . " records"];
	}

	public function index()
    {
		$user = auth()->user();
		$query = $user->profit_and_losses()->with([])->select('profit_and_losses.*');

        return datatables()->of($query)
                            ->addColumn('price', function($profit_and_loss){
                                return $profit_and_loss->item ? $profit_and_loss->item->price : 'N/A';
							})
							
							->addColumn('profit', function($profit_and_loss){
                                return $profit_and_loss->item? 
									   $profit_and_loss->item->price - $profit_and_loss->sales : 'N/A';
							})
							->addColumn('margin', function($profit_and_loss){
								$margin = 0;
								if($profit_and_loss->item && $profit_and_loss->item->price > 0){
                                    $margin = ( 1 - ( $profit_and_loss->sales / $profit_and_loss->item->price ) );
                                    //$margin = ( $profit_and_loss->item->price - $profit_and_loss->sales ) / ( $profit_and_loss->sales );
                                }                                
								return $profit_and_loss->item ? number_format((float) $margin * 100 ,2,'.','') : "N/A";
							})
                            ->toJson();
    }
}
