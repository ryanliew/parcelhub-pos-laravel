<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [];
    
	public function inventory()
	{
		return $this->belongsTo("App\Inventory", "inventory_id");
    }

    public function invoice()
	{
		return $this->belongsTo("App\Invoice", "invoice_no", "invoice_no");
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeIn($query)
    {
        return $query->where('type', 'In');
    }

    public function scopeOut($query)
    {
        return $query->where('type', 'Out');
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', '<=', $date);
    }
}
