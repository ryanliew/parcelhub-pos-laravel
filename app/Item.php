<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $guarded = [];
	
	// protected $appends = ['total_price_after_discount'];
	
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
		if($this->invoice->subtotal == 0)
		{
			return 0;
		}
		else if($this->total_price <= 0)
		{
			return 0;
		}
		else
		{
			$total = round( $this->total_price - ((($this->invoice->discount_value) / ($this->invoice->subtotal)) *  $this->total_price ), 2);
			$processed_total = ( $total * 2 ) * 10; // *2 => 0.5 round up, *10 => get cents digit
			$rounded_total = round( $processed_total );

			$final = $rounded_total > 0 ?
					 $rounded_total / 20 :
					 ;

			return $final;			
		}
		// return $this->invoice->subtotal == 0?
		// 		0:
		// 		round( $this->total_price - ((($this->invoice->discount_value) / ($this->invoice->subtotal)) *  $this->total_price ), 2);
	}
}
