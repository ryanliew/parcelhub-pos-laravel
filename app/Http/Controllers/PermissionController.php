<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function page()
	{
		return view('admin.permissions');
	}

	public function validate_input()
	{
		request()->validate([
            "user_id" => "required|integer",
            "branch_id" => "required|integer",
            "access_level" => "required"
        ]);
	}

    public function index()
    {
    	$query = auth()->user()->is_admin ? Permission::with('branch', 'user')->select('permissions.*') : auth()->user()->current->permissions()->with('branch', 'user')->select('permissions.*');

    	return datatables()->of($query)
                        ->addColumn('branch_name', function(Permission $permission){
                            return $permission->branch->name;
                        })
                        ->addColumn('user_name', function(Permission $permission){
                            return $permission->user->name;
                        })
                        ->addColumn('level', function(Permission $permission){
                            return $permission->type == 'write' ? 'Branch admin' : 'Cashier';
                        })
                        ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $permission = Permission::create([
            'user_id' => request()->user_id,
            'branch_id' => request()->branch_id,
            'type' => request()->access_level
        ]);

    	return json_encode(['message' => "New permission created."]);
    }

    public function update(Permission $permission)
    {
    	$this->validate_input();

        $permission->update([
            'user_id' => request()->user_id,
            'branch_id' => request()->branch_id,
            'type' => request()->access_level
        ]);
    	
    	return json_encode(['message' => "Permission updated"]);
    }

    public function list()
    {
        return Permission::all();
    }

    public function get($permission)
    {
        $result = Permission::where('id', $permission)->get();

        return $result;
    }
}
