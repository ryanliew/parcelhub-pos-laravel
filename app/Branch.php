<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
	protected $guarded = [];
	
    public function users()
    {
    	return $this->hasMany('App\User');
    }

    public function invoices()
    {
    	return $this->hasMany('App\Invoice');
    }
}
