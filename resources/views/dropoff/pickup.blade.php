@extends('layouts.app')

@section('page')
    Pickup
@endsection

@section('content')
    <pickup-form :dropoff="{{ $dropoff }}" @auth :authuser="{{ auth()->user() }}" @endauth></pickup-form>
@endsection
