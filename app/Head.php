<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Head extends Model
{
    protected $guarded = [];

    protected $dates = ['activated_at', 'deactivated_at'];

    protected $appends = ['is_active'];

    protected $with = ['active_session'];

    public function sessions()
    {
    	return $this->hasMany("App\Session");
    }

    public function active_session()
    {
    	return $this->hasOne("App\Session")->where('is_active', true);
    }

    public function getIsActiveAttribute()
    {
    	return $this->activated_at
    			&& (!$this->deactivated_at
    				|| $this->deactivated_at->lessThan($this->activated_at));
    }

    public function activate()
	{
		$this->update([
			'activated_at' => Carbon::now(),
		]);

		$this->sessions()->create([
			'is_active' => true,
			'activated_at' => now(),
			'deactivated_at' => now()->subMinute(),
		]);
	}

	public function deactivate(Invoice $invoice)
	{
		$this->update([
			'deactivated_at' => Carbon::now(),
		]);

		$this->active_session()->update([
			'deactivated_at' => now(),
			'invoice_id' => $invoice->id,
		]);
	}	
}
