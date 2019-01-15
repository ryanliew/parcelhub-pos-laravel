<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $guarded = [];

    public function customers()
    {
    	return $this->hasMany("App\Customer");
    }

    public function products()
    {
    	return $this->belongsToMany("App\Product")->withPivot("walk_in_price", "walk_in_price_special", "corporate_price")->withTimestamps();
    }

    public function sync_products()
    {
    	$details = [];

    	foreach(Product::all() as $product) {

    		$details[$product->id] = [
    									'corporate_price' => $product->corporate_price,
    									'walk_in_price' => $product->walk_in_price,
    									'walk_in_price_special' => $product->walk_in_price_special
    								];

    	}

    	$this->products()->attach($details);
    }
}
