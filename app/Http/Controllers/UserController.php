<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
