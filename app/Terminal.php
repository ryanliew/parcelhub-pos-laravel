<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    protected $guarded = [];

    public function branch()
    {
    	return $this->belongsTo('App\Branch');
    }

    public function invoices()
    {
    	return $this->hasMany('App\Invoice', 'terminal_no');
    }

    public function cashups()
    {
    	return $this->hasMany('App\Cashup');
    }

    public function payments()
    {
        return $this->hasMany('App\Payment', 'terminal_no');
    }
}
