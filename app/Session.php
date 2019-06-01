<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $guarded = [];

    protected $dates = ['activated_at', 'deactivated_at'];

    public function table()
    {
    	return $this->belongsTo("App\Table");
    }

    public function invoices()
    {
    	return $this->hasMany("App\Invoice");
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
