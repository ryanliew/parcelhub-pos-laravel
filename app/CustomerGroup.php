<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $guarded = [];

    public function customers()
    {
    	return $this->hasMany("App\Customer");
    }
}
