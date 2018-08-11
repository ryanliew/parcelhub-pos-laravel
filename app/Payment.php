<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

  	public function branch()
  	{
  		return $this->belongsTo('App\Branch', 'branch_id');
  	}

  	public function customer()
  	{
  		return $this->belongsTo('App\Customer', 'customer_id');
  	}

    public function payments()
    {
      return $this->hasMany('App\PaymentInvoice', 'payment_id');
    }
}
