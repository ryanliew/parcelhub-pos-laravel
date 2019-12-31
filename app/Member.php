<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Newsletter;

class Member extends Model
{
    protected $guarded = [];

    protected $dates = ['birthdate', 'expire_date'];

    protected $appends = ['is_active'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->setIdentifierCode();
            $user->registerToMailchimp();
        });
    }

    public function visits()
    {
        return $this->hasMany("App\Visit");
    }

    public function sessions()
    {
        return $this->hasMany("App\Sessions");
    }

    public function setIdentifierCode()
    {
        $this->update([
            'identifier' => $this->generateReferralCode()
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('expire_date', ">", now()->toDateString());
    }

    public function scopeInactive($query)
    {
        return $query->where('expire_date', "<=", now()->toDateString());
    }

    public function scopeNew($query)
    {
        return $query->whereColumn('expire_data', "<", "created_at");
    }

    public function generateReferralCode()
    {
        $code = rand(pow(10, 4), pow(10, 5)-1);

        if(self::where('identifier', $code)->count() > 0)
            $code = $this->generateReferralCode();

        return $code;
    }

    public function getIsActiveAttribute()
    {
        return $this->expire_date->gt(now());
    }

    public function registerToMailchimp()
    {
        Newsletter::subscribeOrUpdate($this->email, [
            'FNAME' => $this->name, 
            'LNAME' => $this->last_name,
            'PHONE' => $this->phone_number,
        ]);
    }

    public function activate()
    {
        $this->update([
            'expire_date' => now()->addYear(),
        ]);
    }
}
