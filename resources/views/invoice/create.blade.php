@extends('layouts.app')

@section('page')
	New invoice
@endsection

@section('content')
	<div class="container">
		<invoices-create created_by="{{ auth()->id() }}" :auth_user="{{ json_encode(auth()->user()) }}"></invoices-create>
	</div>
@endsection