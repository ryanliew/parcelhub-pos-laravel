<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DropoffItem extends Model
{
    protected $guarded = [];

    public function dropoff()
    {
        return $this->belongsTo("App\Dropoff");
    }
}
