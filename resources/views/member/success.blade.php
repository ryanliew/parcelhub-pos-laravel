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
				<h3>Like our Facebook page below and show this screen to our cashier to enjoy your discounts!</h3>
				<div class="fb-like" data-href="https://www.facebook.com/hexabox.puchong/" data-width="" data-layout="button_count" data-action="recommend" data-size="large" data-share="true"></div>
			</div>
		</div>
	</div>
@endsection