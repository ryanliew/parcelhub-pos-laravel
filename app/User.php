<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use Notifiable, Impersonate;

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

    public function terminal()
    {
        return $this->belongsTo("App\Terminal", "current_terminal");
    }

    public function allowed_users()
    {
        return $this->belongsToMany("App\User", "users_permissions", "user_id", "target_id")->withTimestamps();
    }

    // This is a method that controls admin panel access
    public function isAdmin()
    {
        return $this->is_admin || $this->hasPermission($this->current_branch, 'write');
    }

    public function getDefaultBranchAttribute()
    {
        return $this->branches()->wherePivot('type', 'write')->first();
    }

    public function canBeImpersonated()
    {
        return !$this->is_admin;
    }

    public function hasPermission($branch, $type)
    {   
        return Permission::hasPermission($this->current_branch, $this->id, 'write');
    }
}
