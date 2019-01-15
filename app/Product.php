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

    public function branches()
    {
        return $this->belongsToMany("App\Branch")
                    ->withTimestamps()
                    ->withPivot('id', 'customer_id', 'walk_in_price', 'walk_in_price_special', 'corporate_price');
    }

    public function customer_groups()
    {
      return $this->belongsToMany("App\CustomerGroup")
                  ->withPivot("walk_in_price", "walk_in_price_special", "corporate_price")
                  ->withTimestamps();
    }

    public function getTaxTypeAttribute()
    {
      return $this->tax->code;
    }
}
