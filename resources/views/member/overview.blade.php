@extends('layouts.app')

@section('page')
	Members
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Member</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="members-table">
					<thead>
						<tr>
							<th>First name</th>
							<th>Last name</th>
							<th>Phone number</th>
							<th>Email</th>
							<th>Gender</th>
							<th>Birthdate</th>
							<th>State</th>
							<th>City</th>
							<th>Identifier</th>
							<th>Country</th>
							<th>Address</th>
							<th>Address line 2</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
	
	<members-dialog :data="{{ auth()->user() }}"></members-dialog>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#members-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [
					{
						text: 'Create',
						action: function( e, dt, node, config ) {
							window.events.$emit('createMember');
						}
					},
					@if(auth()->user()->hasPermission(auth()->user()->current_branch, 'write'))
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editMember', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					@endif
					'excel', 'colvis'
				],
				ajax: '{!! route("members.index") !!}',
				columns: [
					{data: 'name'},
					{data: 'last_name'},
					{data: 'phone_number'},
					{data: 'email', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'gender'},
					{data: 'birthdate'},
					{data: 'state'},
					{data: 'city'},
					{data: 'identifier'},
					{data: 'country'},
					{data: 'address_line_1'},
					{data: 'address_line_2'},
				]
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 		@if(auth()->user()->hasPermission(auth()->user()->current_branch, 'write'))
			        table.button( 1 ).enable( selectedRows === 1 );
			        table.button( 2 ).enable( selectedRows > 0 );
			    @else
			    		table.button( 0 ).enable( selectedRows > 0 );
			    @endif
		    });

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

		
	</script>
@endsection