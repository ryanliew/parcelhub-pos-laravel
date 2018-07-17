<?php

namespace App\Http\Controllers;

use App\Terminal;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    public function page()
	{
		return view('admin.terminals');
	}

	public function validate_input()
	{
		request()->validate([
            "float" => "required|integer",
            "branch_id" => "required|integer"
        ]);
	}

    public function index()
    {
    	return datatables()->of(Terminal::with('branch')->select('terminals.*'))
                        ->addColumn('branch_name', function(Terminal $terminal){
                            return $terminal->branch->name;
                        })
                        ->toJson();	
    }

    public function store()
    {
    	$this->validate_input();

        $terminal = Terminal::create([
            'name' => request()->name,
            'float' => request()->float,
            'is_active' => request()->has('is_active'),
            'branch_id' => request()->branch_id
        ]);

    	return json_encode(['message' => "New terminal created."]);
    }

    public function update(Terminal $terminal)
    {
    	$this->validate_input();

        $terminal->update([
            'name' => request()->name,
            'float' => request()->float,
            'is_active' => request()->has('is_active'),
            'branch_id' => request()->branch_id
        ]);
    	
    	return json_encode(['message' => "Terminal updated"]);
    }

    public function list()
    {
        return Terminal::all();
    }

    public function get($terminal)
    {
        $result = Terminal::where('id', $terminal)->get();

        return $result;
    }
}
