<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = [];

    public function member()
    {
    	return $this->belongsTo("App\Member");
    }
}
