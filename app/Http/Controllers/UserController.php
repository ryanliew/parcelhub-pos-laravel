<?php

namespace App\Http\Controllers;

use App\Branch;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Lab404\Impersonate\Models\impersonate;

class UserController extends Controller
{
    public function change_branch()
    {   
        $branch = Branch::find(request()->branch);

        auth()->user()->update(['current_branch' => request()->branch, 'current_terminal' => $branch->terminals()->first()->id]);

    }

    public function change_terminal()
    {
        auth()->user()->update(['current_terminal' => request()->terminal]);
    }

    public function change_user()
    {
        auth()->user()->impersonate(User::find(request()->user));
    }

    public function list()
    {
        return User::all();
    }

    public function get_impersonation()
    {
        $users = collect(explode( ",", request()->allowed));    

        $result = $users->map(function($user, $key){
            return User::find($user);
        });

        return json_encode($result);
    }

    public function check_impersonation()
    {
        $manager = app('impersonate');

        return json_encode($manager->isImpersonating());
    }

    public function leave_impersonation()
    {
        $manager = app('impersonate');

        $manager->leave();
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
            "current_terminal" => "sometimes|required|integer"
        ]);
	}

    public function index()
    {
        $query = auth()->user()->is_admin 
                ? User::with(['current', 'terminal'])
                : User::with(['current', 'terminal'])
                    ->select('users.*')
                    ->whereRaw('(SELECT COUNT(*) FROM permissions WHERE users.is_admin = 0 AND user_id = users.id AND branch_id = ' . auth()->user()->current->id. ') > 0');

    	return datatables()->of($query)->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $store = [
            "username" => request()->username,
            "name" => request()->name,
            "email" => request()->email,
            "password" => bcrypt(request()->password),
            "current_branch" => request()->current_branch,
            "current_terminal" => request()->has('current_terminal') 
                                ? request()->current_terminal 
                                : Branch::find(request()->current_branch)->terminals()->first()->id,
            "is_suspended" => request()->is_suspended,
        ];

        $user = User::create($store);

        $user->giveDefaultPermission();

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
        	"current_terminal" => request()->current_terminal,
            "is_suspended" => request()->is_suspended,
        ];

        if(request()->has('password'))
        	$update["password"] = bcrypt(request()->password);

        $user->update($update);
    	
    	return json_encode(['message' => "User updated"]);
    }

    public function loginAs(User $user)
    {
        return view("auth.impersonation");
    }

    public function grantAccess(User $user)
    {
        request()->validate(['username' => 'required|exists:users,username', 'password' => 'required']);

        if(auth()->user()->username == request()->username)
            return redirect()->back()->withErrors('You are already logged in!');

        // Find a user to authenticate
        $target = User::where('username', request()->username)
                    ->get()
                    ->first();


        // Authenticated, add to permission
        if(Hash::check(request()->password, $target->password)) {

            $existing = Cookie::get('allowed_users');

            $value = $existing ? $existing : auth()->id();

            $value .= "," . $target->id;

            $cookie = cookie('allowed_users', $value, 240);
            // auth()->user()->allowed_users()->attach($target);
            auth()->user()->impersonate($target);
        }
        else {
            return redirect()->back()->withErrors(['username' => 'Credentials does not match with our database']);
        }

        return redirect()->route('invoices.page')->cookie($cookie);

    }
}
