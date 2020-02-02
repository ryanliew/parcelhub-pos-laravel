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

    public function list_inactive()
    {
        return view('member.inactive', ['members' => Member::inactive()->get()]);
    }

    public function page()
    {
    	return view('member.overview');
    }

    public function register()
    {
        return view('member.page');
    }

    public function index()
    {
        $query = Member::query();

        return datatables()->of($query)
                            ->toJson();
    }

    public function search()
    {
        return Member::where('phone_number', request()->keyword)
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
            "last_name" => "required",
            "phone_number" => ["required", $phone_unique, 'regex:/^(\+6?01?)[0-46-9]*[0-9]{7,8}$/'],
            "email" => ["nullable", "required", $mail_unique],
            "gender" => "required",
            "birthdate" => "required",
            "city" => "required",
            "state" => "required",
        ], [
            'phone_number.regex' => "Incorrect format. Please follow this format: +60121234567"
        ]);
    }

    public function store()
    {
        $this->validate_input();

        request()->merge(['expire_date' => now()->subDay()]);

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
        if($member->is_active)
            return $member;
    }

    public function success(Member $member)
    {
        return view("member.success", ['member' => $member]);
    }

    public function activate_member(Member $member)
    {
        $member->activate();

        return redirect('/members/inactive');
    }
}
