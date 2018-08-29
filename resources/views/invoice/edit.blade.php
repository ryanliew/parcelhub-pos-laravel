@extends('layouts.app')

@section('page')
	Edit invoice - {{ $invoice->displayText }}
@endsection

@section('content')
	<div class="container-fluid">
		<invoices-create created_by="{{ auth()->id() }}" invoice="{{ $invoice->id }}"></invoices-create>
	</div>
@endsection