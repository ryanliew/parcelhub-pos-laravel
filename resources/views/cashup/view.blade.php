@extends('layouts.app')

@section('page')
	Cashup details
@endsection

@section('content')
	<div class="container">
		<cashup-details :cashup="{{ $cashup }}">
		</cashup-details>
	</div>
@endsection