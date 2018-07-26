<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	protected $guarded = [];

    protected $appends = ['can_edit'];
	
    public function branch()
    {
    	return $this->belongsTo('App\Branch');
    }

    public function terminal()
    {
        return $this->belongsTo('App\Terminal', 'terminal_no');
    }

    public function items()
    {
    	return $this->hasMany('App\Item');
    }

    public function user()
    {
    	return $this->belongsTo("App\User", "created_by");
    }

    public function customer()
    {
        return $this->belongsTo("App\Customer", "customer_id");
    }

    public function payment()
    {
        return $this->hasMany('App\Payment', "invoice_no");
    }

    public function cashup()
    {
        return $this->belongsTo("App\Cashup");
    }

    public function scopeCashupRequired($query)
    {
        return $query->where('cashed', false)->where('type', 'Cash');
    }

    public function getCanEditAttribute()
    {
        $setting = Setting::find(1);

        return $setting->lock_date->gte($this->created_at);
    }
}
