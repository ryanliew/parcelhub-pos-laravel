<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	protected $guarded = [];

    protected $appends = ['can_edit'];

    protected $dates = ['canceled_on'];
	
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
        return $this->hasMany('App\PaymentInvoice', "invoice_no", "invoice_no");
    }

    public function cashup()
    {
        return $this->belongsToMany("App\Cashup")->withTimestamps()->withPivot('total', 'payment_method', 'payment_id');
    }

    public function scopeCashupRequired($query)
    {
        return $query->where('cashed', false);
    }

    public function scopeCanceled($query)
    {
        return $query->whereNotNull('canceled_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('canceled_by');
    }

    public function getCanEditAttribute()
    {
        /*
        
        $setting = Setting::find(1);

        return $setting->lock_date->lte($this->created_at);
        */
       
       return false; // Disallow edit for now
    }

    public function getRoundingAttribute()
    {
        $actual_total =  $this->subtotal - $this->discount_value;

        $rounding_value = $this->total - $actual_total;
        if($this->total + $this->discount_value > $actual_total)
            return $rounding_value;

        return -$rounding_value;
    }

    public function getDisplayTextAttribute()
    {
        return $this->invoice_no;
    }

    public function getReceiptTypeAttribute()
    {
        return $this->customer_id ? "Tax Invoice" : "Receipt";
    }
}
