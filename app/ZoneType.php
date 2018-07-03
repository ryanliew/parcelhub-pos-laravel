<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoneType extends Model
{
    protected $guarded = [];

    public function zones()
    {
    	return $this->hasMany("App\Zone");
    }
}
