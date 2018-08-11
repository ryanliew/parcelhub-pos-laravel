<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
	protected $guarded = [];

    public function invoice()
  	{
  		return $this->belongsTo('App\Invoice', 'invoice_no', 'invoice_no');
  	}

  	public function payment()
  	{
  		return $this->belongsTo('App\Payment', 'payment_id', 'id');
  	}
}
