<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany("App\BillingItem");
    }

    public function branch()
    {
        return $this->belongsTo("App\Branch");
    }
}
