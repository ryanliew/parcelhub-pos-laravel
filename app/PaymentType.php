<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $guarded = [];

    public function invoice()
    {
    	return $this->hasMany('App\Invoice');
    }
}
