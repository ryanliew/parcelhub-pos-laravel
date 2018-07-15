<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function change_branch()
    {
        auth()->user()->update(['current_branch' => request()->branch]);
    }

    public function change_terminal()
    {
        auth()->user()->update(['current_terminal' => request()->terminal]);
    }

    public function page()
	{
		return view('admin.users');
	}

	public function validate_input($user_id = "")
	{
		$rule = Rule::unique("users");
		if($user_id) {
			$rule->ignore($user_id);
		}
		request()->validate([
			"name" => 'required',
            "username" => ["required", $rule],
            "email" => ["required","email", $rule],
            "password" => "sometimes|confirmed",
            "current_branch" => "required|integer",
            "current_terminal" => "required|integer"
        ]);
	}

    public function index()
    {
    	return datatables()->of(User::with('current'))->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $branch = User::create(request()->all());

    	return json_encode(['message' => "New user created."]);
    }

    public function update(User $user)
    {
    	$this->validate_input($user->id);

    	$update = [
        	"username" => request()->username,
        	"name" => request()->name,
        	"email" => request()->email,
        	"current_branch" => request()->current_branch,
        	"current_terminal" => request()->current_terminal
        ];

        if(request()->has('password'))
        	$update["password"] = bcrypt(request()->password);

        $user->update($update);
    	
    	return json_encode(['message' => "User updated"]);
    }
}
