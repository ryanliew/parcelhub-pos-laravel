<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function current()
    {
        return $this->belongsTo('App\Branch', 'current_branch');
    }

    public function branches()
    {
        return $this->belongsToMany('App\Branch', 'permissions')->as('permission')->withPivot('type')->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany("App\Invoice", "created_by");
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function getDefaultBranchAttribute()
    {
        return $this->branches()->wherePivot('type', 'write')->first();
    }
}
