<?php

namespace App\Http\Controllers;

use App\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function list()
    {
   	
        return Member::orderBy('name')->get();
    }

    public function page()
    {
    	return view('member.overview');
    }

    public function index()
    {
        $query = Member::query();

        return datatables()->of($query)
                            ->toJson();
    }

    public function search()
    {
        return Member::where('phone_number', 'LIKE', '%' . request()->keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . request()->keyword . '%')
                        ->get();

    }


    public function validate_input($ignore = null)
    {
 		$mail_unique = Rule::unique('members', 'email');
 		$phone_unique = Rule::unique('members', 'phone_number');

    	if($ignore) {
    		$mail_unique->ignore($ignore);
    		$phone_unique->ignore($ignore);
    	}

        request()->validate([
            "name" => "required",
            "phone_number" => ["required", $phone_unique],
            "email" => ["nullable", $mail_unique],
            "gender" => "required"
        ]);
    }

    public function store()
    {
        $this->validate_input();

        $member = Member::create(request()->all());

        return json_encode(['message' => "Member created", "member" => $member]);
    }

    public function update(Member $member)
    {
        $this->validate_input($member->id);

        $member->update(request()->all());
        
        return json_encode(['message' => "Member updated"]);
    }

    public function get(Member $member)
    {
        return $member;
    }
}
