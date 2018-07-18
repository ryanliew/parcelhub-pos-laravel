<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    protected $guarded = [];

    public function branch()
    {
    	return $this->belongsTo('App\Branch');
    }

}
