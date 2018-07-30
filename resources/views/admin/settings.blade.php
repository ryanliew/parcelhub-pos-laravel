@extends('layouts.admin')

@section('page')
	Global parameters
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Global parameters</b>
			</div>
			<div class="card-body">
				<form action="{{ route('settings') }}" method="POST">
					@csrf
					<div class="form-group">
						<label for="lock-date">Invoice lock in date <span class="text-danger">*</span></label>
						<input type="date" class="form-control" id="lock-date" name="lock_date" value="{{ $setting->lock_date->toDateString() }}">
						@if ($errors->has('lock_date'))
                            <span class="text-danger" role="alert">
                                <strong>{{ $errors->first('lock_date') }}</strong>
                            </span>
                        @endif
					</div>

					<button type="submit" class="btn btn-primary">Save</button>
				</form>
			</div>
		</div>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
			
		@if(session('success'))
			flash("{{ session('success')}}")
		@endif
		
	</script>
@endsection