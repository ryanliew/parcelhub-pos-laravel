<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

  	public function product_type()
  	{
  		return $this->belongsTo('App\ProductType');
  	}

  	public function items()
  	{
  		return $this->hasMany('App\Item');
  	}
}
