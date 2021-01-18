<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dropoff extends Model
{
    public const STATUS_NEW = "New";
    public const STATUS_COMPLETE = "Complete";
    public const TYPE_DROPOFF = "Dropoff";

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo("App\Customer");
    }

    public function vendor()
    {
        return $this->belongsTo("App\Vendor");
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function branch()
    {
        return $this->belongsTo("App\Branch");
    }

    public function terminal()
    {
        return $this->belongsTo("App\Terminal");
    }

    public function items()
    {
        return $this->hasMany("App\DropoffItem");
    }

    public function pickup()
    {
        $this->update([
            "status" => self::STATUS_COMPLETE,
            "picked_up_at" => now(),
        ]);
    }

    public function getPickupUrlAttribute()
    {
        return route("dropoff.pickup", ["dropoff" => $this->id]);
    }
}
