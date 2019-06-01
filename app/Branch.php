<?php

namespace App;

use App\Permission;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
	protected $guarded = [];
	
    protected static function boot()
    {
        parent::boot();

        static::created(function ($branch){
            $branch->create_default_terminal();

            $branch->create_default_sequence();

            $branch->create_default_user($terminal->id);
        });
    }

    public function invoices()
    {
    	return $this->hasMany('App\Invoice');
    }

    public function tables()
    {
        return $this->hasMany("App\Table");
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'permissions', 'branch_id', 'user_id')->withPivot('type')->as('permission')->withTimestamps();
    }

    public function permissions()
    {
        return $this->hasMany("App\Permission");
    }

    public function payments()
    {
        return $this->hasMany('App\Payment', 'branch_id');
    }

    public function customers()
    {
        return $this->hasMany('App\Customer','branch_id');
    }

    public function terminals()
    {
        return $this->hasMany('App\Terminal');
    }

    public function sequence()
    {
        return $this->hasOne('App\Sequence');
    }

    public function products()
    {
        return $this->belongsToMany("App\Product")
                    ->withTimestamps()
                    ->withPivot('id', 'customer_id', 'walk_in_price', 'walk_in_price_special', 'corporate_price', 'is_tax_inclusive');
    }

    public function cashups()
    {
        return $this->hasMany("App\Cashup");
    }

    public function groups()
    {
        return $this->hasMany("App\CustomerGroup");
    }

    public function default_product_type()
    {
        return $this->belongsTo("App\ProductType", 'product_type_id');
    }
    
    public function create_default_user($terminal)
    {
        $user = User::create([
            'username' => $this->code,
            'email' => $this->email,
            'password' => bcrypt('parcelhub1'),
            'is_staff' => true,
            'name' => $this->owner,
            'current_branch' => $this->id,
            'current_terminal' => $terminal
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


