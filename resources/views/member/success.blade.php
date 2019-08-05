@extends('layouts.app')

@section('page')
	Welcome to the club!
@endsection

@section('content')
	<div class="container pt-5">
		<div class="card">
			<div class="card-body text-center">
				<i class="fa fa-check fa-4x text-success py-3"></i> <br>
				<h1>Welcome {{ $member->name }}!</h1>
				<h3>Tell our cashier your phone number or "{{ $member->identifier }}" to enjoy your discounts!</h3>
			</div>
		</div>
	</div>
@endsection