<?php

namespace App\Http\Controllers;

use App\ZoneType;
use Illuminate\Http\Request;

class ZoneTypeController extends Controller
{
    public function list()
    {
    	return ZoneType::all();
    }
}
