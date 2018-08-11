<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/invoices/create';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function logout()
    {
        $logging_out = collect([request()->user]);

        $allowed_users = collect(explode("," ,request()->allowed_users));

        $remaining_users = $allowed_users->diff($logging_out);

        Auth::logout();

        $cookie = Cookie::forget('allowed_users');

        if($remaining_users->count() > 0) {
            $cookie = cookie('allowed_users', $remaining_users->implode(","), 240);

            Auth::loginUsingId( $remaining_users->first() );
        }


        return redirect()->route('invoices.page')->cookie($cookie);
    }

    public function redirectTo()
    {
        return "/invoices/create";
    }
}
