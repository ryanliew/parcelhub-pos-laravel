<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
	protected $guarded = [];
	
	// public function products()
	// {
	// 	return $this->belongsToMany('App\Product', 'inventory_products', 'id', 'product_id')->withTimestamps()->withPivot('quantity');
	// }

	public function stocks()
	{
		return $this->hasMany("App\Stock", "inventory_id");
	}

	public function inventoryProducts()
	{
		return $this->hasMany("App\InventoryProduct", "inventory_id");
	}
 
	public function getStockCountAttribute()
	{
        return $this->stocks()->active()->in()->sum('quantity')
          		- $this->stocks()->active()->out()->sum('quantity');
    }
    
    public function get_stock_count_on_date($date)
	{
        return $this->stocks()->active()->byDate($date)->in()->sum('quantity')
            - $this->stocks()->active()->byDate($date)->out()->sum('quantity');
	}
}
