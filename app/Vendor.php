<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
	protected $guarded = [];
  	public function zone_type()
  	{
  		return $this->belongsTo("App\ZoneType");
	}
	  
	public function products()
  	{
  		return $this->hasMany('App\Product');
	}	 
	
	public function items()
  	{
  		return $this->hasManyThrough('App\Item', 'App\Product');
	}
	
	public static function boot() {
        parent::boot();

        static::deleting(function($vendor) { 
             $vendor->products()->delete();
        });
    }
}
