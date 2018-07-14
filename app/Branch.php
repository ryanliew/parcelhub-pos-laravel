<?php

namespace App;

use App\Permission;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
	protected $guarded = [];
	
    public function invoices()
    {
    	return $this->hasMany('App\Invoice');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'permissions', 'branch_id', 'user_id')->withPivot('type')->as('permission')->withTimestamps();
    }

    public function customers()
    {
        return $this->hasMany('App\Customer','branch');
    }

    public function create_default_user()
    {
        $user = User::create([
            'username' => $this->code,
            'email' => $this->email,
            'password' => bcrypt('parcelhub1'),
            'is_staff' => true,
            'name' => $this->owner,
            'current_branch' => $this->id,
            'current_terminal' => 1
        ]);

        $this->grant_permission($user, Permission::Write);
    }

    public function grant_permission($user, $type)
    {
        $this->users()->attach($user, [
            'type' => $type
        ]);
    }
}


