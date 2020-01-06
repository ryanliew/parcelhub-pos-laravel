<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $guarded = [];
	
	protected $appends = ['total_price_after_discount'];
	
	public function product()
	{
		return $this->belongsTo('App\Product');
	}

	public function invoice()
	{
		return $this->belongsTo("App\Invoice");
	}
 
	public function getTotalPriceAfterDiscountAttribute()
	{
		return $this->total_price - ((($this->invoice->discount_value) / ($this->invoice->subtotal)) *  $this->total_price );
	}
}
