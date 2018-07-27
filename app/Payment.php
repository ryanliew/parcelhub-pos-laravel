<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

  	public function invoice()
  	{
  		return $this->belongsTo('App\Invoice', 'invoice_no', 'invoice_no');
  	}

  	public function branch()
  	{
  		return $this->belongsTo('App\Branch', 'branch_id');
  	}

  	public function customer()
  	{
  		return $this->belongsTo('App\Customer', 'customer_id');
  	}
}
