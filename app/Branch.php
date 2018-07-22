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

    public function payments()
    {
        return $this->hasMany('App\Payment', 'branch_id');
    }

    public function customers()
    {
        return $this->hasMany('App\Customer','branch');
    }

    public function terminals()
    {
        return $this->hasMany('App\Terminal');
    }

    public function sequence()
    {
        return $this->hasOne('App\Sequence');
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

        return $user;
    }

    public function create_default_terminal()
    {
        $terminal = $this->terminals()->create([
            'name' => 'Drawer A',
            'float' => 0.00,
            'is_active' => true,
        ]);

        return $terminal;
    }

    public function create_default_sequence()
    {
        $sequence = $this->sequence()->create();

        return $sequence;
    }

    public function grant_permission($user, $type)
    {
        $this->users()->attach($user, [
            'type' => $type
        ]);
    }
}


