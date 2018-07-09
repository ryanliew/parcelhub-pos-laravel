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

    public function vendor()
    {
        return $this->belongsTo("App\Vendor");
    }

    public function tax()
    {
        return $this->belongsTo("App\Tax");
    }

    public function zone_type()
    {
        return $this->belongsTo("App\ZoneType");
    }

  	public function items()
  	{
  		return $this->hasMany('App\Item');
  	}
}
