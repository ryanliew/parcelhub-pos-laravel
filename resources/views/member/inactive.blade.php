@extends('layouts.app')

@section('page')
	Inactive members
@endsection


@section('content')
	<div class="container">
		@foreach($members as $member)
			<div class="card mb-2">
				<div class="card-header">
					<b>{{ $member->name }} <small>({{ $member->identifier }})</small></b>
				</div>

				<div class="card-body">
					<div class="row">
						<div class="col">
							Registration date: {{ $member->created_at }}
						</div>
						<div class="col">
							Expire date: {{ $member->expire_date }}
						</div>
					</div>
					<div class="row">
						<div class="col">
							Phone: {{ $member->phone_number }}
						</div>
						<div class="col">
							Email: {{ $member->email }}
						</div>
					</div>
				</div>

				<div class="card-footer">
					<form method="POST" action="/members/activate/{{ $member->id }}">
						{{ csrf_field() }}
						<button class="btn btn-primary" type="submit">Activate member</button>
					</form>
				</div>
			</div>
		@endforeach
	</div>
@endsection
