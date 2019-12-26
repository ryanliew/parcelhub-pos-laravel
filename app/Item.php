<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];
	
	public function product()
	{
		return $this->belongsTo('App\Product');
	}

	public function product_type()
	{
		return $this->belongsTo("App\ProductType");
	}

	public function member()
	{
		return $this->belongsTo("App\Member");
	}
	
	public function session()
	{
		return $this->belongsTo("App\Session");
	}
}
