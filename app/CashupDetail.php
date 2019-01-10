<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashupDetail extends Model
{
	protected $guarded = [];
	
    public function cashup()
    {
    	return $this->belongsTo("App\Cashup");
    }
}
