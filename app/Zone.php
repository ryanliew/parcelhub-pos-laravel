<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $guarded = [];

    public function zone_type()
    {
    	return $this->belongsTo("App\ZoneType");
    }

    public function products()
    {
    	return $this->hasMany("App\Product");
    }
}
