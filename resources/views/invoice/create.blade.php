@extends('layouts.app')

@section('page')
	New invoice
@endsection

@section('content')
	<div class="container-fluid">
		<hexaform :branch="{{ auth()->user()->current }}"></hexaform>
		{{-- <invoices-create :is_edit="false" created_by="{{ auth()->id() }}" :auth_user="{{ json_encode(auth()->user()) }}" :default_product_type="{ value: {{ auth()->user()->current->product_type_id }}, label: '{{ auth()->user()->current->default_product_type->name }}', has_detail: '{{ auth()->user()->current->default_product_type->has_detail }}'  }"></invoices-create> --}}
	</div>
@endsection