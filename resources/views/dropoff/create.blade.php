@extends('layouts.app')

@section('page')
    New Drop Off
@endsection

@section('content')
    <div class="container-fluid">
        <dropoff-form :auth_user="{{ json_encode(auth()->user()) }}"></dropoff-form>
    </div>
@endsection