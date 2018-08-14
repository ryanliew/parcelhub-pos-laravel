@extends('layouts.app')

@section('page')
	Customers
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Customer</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="customers-table">
					<thead>
						<tr>
							<th>Type</th>
							<th>Name</th>
							<th>Contact</th>
							<th>Email</th>
							<th>Fax</th>
							<th>Registration/IC No</th>
							<th>Address Line 1</th>
							<th>Address Line 2</th>
							<th>Address Line 3</th>
							<th>Address Line 4</th>
							<th>Branch</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>

	
	<customers-dialog :data = "{{ auth()->user() }}"></customers-dialog>
	<statement-dialog :data = "{{ auth()->user() }}"></statement-dialog>
	

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#customers-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [
		 			@if(auth()->user()->hasPermission(auth()->user()->current_branch, 'write'))
					{
						text: 'Create',
						action: function( e, dt, node, config ) {
							window.events.$emit('createCustomer');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editCustomer', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					@endif
					{
						text: 'Generate statement',
						action: function( e, dt, node, config ) {
							window.events.$emit('generateStatement', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					'excel', 'colvis'
				],
				ajax: '{!! route("customers.index") !!}',
				columns: [
					{data: 'type'},
					{data: 'name'},
					{data: 'contact'},
					{data: 'email', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'fax', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'registration_no', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'address1', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'address2', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'address3', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'address4', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'branch.name'} ,
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