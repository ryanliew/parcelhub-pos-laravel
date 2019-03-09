<?php

namespace App;

use App\CustomerGroup;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($product){
            $product->add_to_groups();
        });
    }

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

    public function add_to_groups()
    {
        $groups = CustomerGroup::all();

        $details = [];

        foreach($groups as $group) {
            $details[$group->id] = ['walk_in_price' => $this->walk_in_price, 
                                    'walk_in_price_special' => $this->walk_in_price_special, 
                                    'corporate_price' => $this->corporate_price,
                                    ];
        }

        $this->customer_groups()->attach($details);
    }
}
