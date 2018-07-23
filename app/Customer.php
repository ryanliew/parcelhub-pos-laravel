<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    protected $guarded = [];

    public function branch()
    {
    	return $this->belongsTo('App\Branch', 'branch_id');
    }

    public function invoices()
    {
    	return $this->hasMany('App\Invoice', 'customer_id');
    }
}
