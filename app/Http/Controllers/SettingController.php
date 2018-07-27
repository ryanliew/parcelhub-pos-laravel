<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
    	return Setting::find(1);
    }

    public function view()
    {
    	return view('admin.settings', ['setting' => Setting::find(1)]);
    }

    public function store()
    {
    	request()->validate(['lock_date' => 'required']);
    	
    	$setting = Setting::find(1);

    	$setting->update(["lock_date" => request()->lock_date]);

    	return back()->with("success", "Settings saved");
    }
}
