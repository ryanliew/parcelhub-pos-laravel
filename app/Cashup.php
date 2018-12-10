<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cashup extends Model
{
	protected $guarded = [];

	protected $dates = ['created_at', 'updated_at', 'session_start'];

	public function branch()
	{
		return $this->belongsTo("App\Branch");
	}
	
    public function terminal()
    {
    	return $this->belongsTo("App\Terminal");
    }

    public function creator()
    {
    	return $this->belongsTo("App\User", 'created_by');
    }

    public function invoices()
    {
    	return $this->belongsToMany("App\Invoice")->withTimestamps()->withPivot('total', 'payment_method', 'payment_id');
    }

    public function payments()
    {
        return $this->hasMany("App\Payment");
    }

    public function details()
    {
        return $this->hasMany("App\CashupDetail");
    }
}
