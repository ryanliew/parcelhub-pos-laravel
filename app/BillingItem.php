<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingItem extends Model
{
    protected $guarded = [];

    public function billing()
    {
        return $this->belongsTo("App\Billing");
    }
}
