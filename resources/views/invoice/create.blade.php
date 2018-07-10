@extends('layouts.app')

@section('page')
	New invoice
@endsection

@section('content')
	<div class="container">
		<invoices-create created_by="{{ auth()->id() }}"></invoices-create>
	</div>
@endsection