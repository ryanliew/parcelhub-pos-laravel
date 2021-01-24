<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingRecord extends Model
{
    protected $guarded = [];

    public function import()
    {
        return $this->belongsTo("App\BillingImport","billing_imports_id");
    }
}
