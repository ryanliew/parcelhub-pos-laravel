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
}
