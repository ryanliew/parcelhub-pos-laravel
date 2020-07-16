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
		return $this->quantity > 0 ? $this->inventory->stock_count / $this->quantity : 0;
    }
    
    public function get_max_quantity_on_date($date)
	{
		return $this->quantity > 0 ? $this->inventory->get_stock_count_on_date($date) / $this->quantity : 0;
	}
}
