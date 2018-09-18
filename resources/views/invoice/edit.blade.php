@extends('layouts.app')

@section('page')
	Edit invoice - {{ $invoice->displayText }}
@endsection

@section('content')
	<div class="container-fluid">
		<invoices-create created_by="{{ auth()->id() }}" invoice="{{ $invoice->id }}" :default_product_type="{ value: {{ auth()->user()->current->product_type_id }}, label: '{{ auth()->user()->current->default_product_type->name }}', has_detail: '{{ auth()->user()->current->default_product_type->has_detail }}'  }"></invoices-create>
	</div>
@endsection