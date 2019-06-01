<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $guarded = [];

    protected $appends = ["is_active"];

    public function branch()
    {
    	return $this->belongsTo("App\Branch");
    }

    public function sessions()
    {
    	return $this->hasMany("App\Session");
    }

    public function getIsActiveAttribute()
    {
    	return $this->sessions()->active()->count() > 0;
    }

    public function toggleStatus()
    {
    	if($this->is_active) {

    		$this->sessions()->active()->first()->deactivate();
    		
    	} else {

    		$session = $this->sessions()->create([
    			'is_active' => true, 
    			'activated_at' => Carbon::now(),
    			'deactivated_at' => Carbon::now(),
    		]);

    		$session->activate();
    	}
    }
}
