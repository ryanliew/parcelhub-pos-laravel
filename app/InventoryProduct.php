<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryProduct extends Model
{
	protected $guarded = [];
	
	public function product()
	{
		return $this->hasOne('App\Product', 'id', 'product_id');
	}

	public function inventory()
	{
		return $this->belongsTo("App\Inventory", "inventory_id", "id");
	}
 
	public function getMaxQuantityAttribute()
	{
        return 0; 
    }
    
    public function get_stock_count_on_date($date)
	{
        return 0; 
        // $this->stocks()->active()->byDate($date)->in()->sum('quantity')
        //     - $this->stocks()->active()->byDate($date)->out()->sum('quantity');
	}
}
