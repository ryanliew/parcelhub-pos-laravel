<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	protected $guarded = [];
	
    public function branch()
    {
    	return $this->belongsTo('App\Branch');
    }

    public function items()
    {
    	return $this->hasMany('App\Item');
    }

    public function user()
    {
    	return $this->belongsTo("App\User", "created_by");
    }
}
