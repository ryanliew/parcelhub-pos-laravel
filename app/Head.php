<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Head extends Model
{
    protected $guarded = [];

    protected $dates = ['activated_at', 'deactivated_at'];

    protected $appends = ['is_active'];

    public function getIsActiveAttribute()
    {
    	return $this->activated_at
    			&& (!$this->deactivated_at
    				|| $this->deactivated_at->lessThan($this->activated_at));
    }

    public function activate()
	{
		$this->update([
			'activated_at' => Carbon::now()->subHour(),
		]);
	}

	public function deactivate()
	{
		$this->update([
			'deactivated_at' => Carbon::now(),
		]);
	}	
}
