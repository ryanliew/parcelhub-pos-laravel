<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $guarded = [];

    public function products()
    {
    	return $this->hasMany("App\Product");
    }

    public function vendor()
    {
    	return $this->belongsTo("App\Vendor", "default_vendor_id");
    }

    public function zonetype()
    {
    	return $this->belongsTo("App\ZoneType", "default_zone_type_id");
    }
}
