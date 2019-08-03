@extends('layouts.app')

@section('page')
	View session
@endsection

@section('content')
	<div class="container-fluid">
		<order :session="{{ $session }}" :table="{{ $session->table }}"></order>
	</div>
@endsection