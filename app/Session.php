<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $guarded = [];

    protected $dates = ['activated_at', 'deactivated_at'];

    protected $with = ['items'];

    // public function table()
    // {
    // 	return $this->belongsTo("App\Table");
    // }

    public function invoices()
    {
    	return $this->belongsTo("App\Invoice");
    }

    public function head()
    {
        return $this->belongsTo("App\Head");
    }

    public function items()
    {
        return $this->hasMany("App\Item");
    }

    public function member()
    {
        return $this->belongsTo("App\Member");
    }
    /*
     *  Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
     *  Methods
     */

    public function activate()
    {
        $this->update([
            'is_active' => true,
            'activated_at' => Carbon::now(),
        ]);
    }

    public function deactivate()
    {
        $this->update([
            'is_active' => false,
            'deactivated_at' => Carbon::now(),
        ]);
    }
}
