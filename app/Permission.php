<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	protected $guarded = [];

    const Read = 'read';
    const Write = 'write';

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function branch()
    {
    	return $this->belongsTo('App\Branch');
    }

    public static function hasPermission($branch, $user, $type)
    {
    	return Permission::where('branch_id', $branch)
    						->where('user_id', $user)
    						->where('type', $type)
    						->count() > 0;
    }

    public function scopeWrite($query)
    {
        return $query->where('type', 'write');
    }
}
