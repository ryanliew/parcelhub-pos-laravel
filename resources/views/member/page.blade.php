@extends('layouts.app')

@section('page')
	Register as member
@endsection

@section('content')
	<div class="container pt-5">
		<div class="card">
			<div class="card-header">
				<b>Join as member!</b>
			</div>
			<div class="card-body">
				<members-form
					:is-edit="false"
					url="/members"
					ref="memberForm"
					confirm-message="Terms and conditions"
					secondary-message="By clicking on the OK button below I agree to the terms an conditions of Hexabox Boardgame Cafe"
					:self-submit="true"
					:should-redirect="true"
					>
					
				</members-form>
			</div>
		</div>
	</div>
@endsection