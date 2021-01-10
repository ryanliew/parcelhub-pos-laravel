@extends('layouts.app')

@section('page')
    Pickup
@endsection

@section('content')
    <pickup-form :dropoff="{{ $dropoff }}" :authuser="{{ auth()->user() }}"></pickup-form>
@endsection
