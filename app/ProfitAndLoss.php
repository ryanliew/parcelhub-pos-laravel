<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfitAndLoss extends Model
{
    //
    protected $guarded = [];

    public function item()
    {
    	return $this->belongsTo('App\Item', 'tracking_code', 'tracking_code')
    				->leftJoin('invoices', 'invoices.id', '=', 'invoice_id')
    				->where('branch_id', auth()->user()->current_branch);
    }
}
