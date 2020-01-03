<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfitAndLoss extends Model
{
    //
    protected $guarded = [];

    public function item()
    {
    	return $this->belongsTo('App\Item', 'tracking_code', 'tracking_code');
    }
}
