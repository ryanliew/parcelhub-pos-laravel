@extends('layouts.app')

@section('page')
	Customer groups
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Customer groups</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="groups-table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Customer count</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<groups-dialog :user="{{ auth()->user() }}"></groups-dialog>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#groups-table").DataTable({
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
							window.events.$emit('createCustomerGroup');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editCustomerGroup', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					// Enable the following if delete is allowed
					{
						text: 'Delete',
						action: function( e, dt, node, config ) {
							swalalert("Are you sure?", "", 'warning', function(){
								var item = table.rows({selected: true}).data().toArray()[0];
						        
								axios.delete("/groups/" + item.id)
									.then(response => window.events.$emit('deletedCustomerGroup', response));
							});
						},
						enabled: false
					},

					{
						text: 'Edit product price',
						action: function( e, dt, node, config ) {
							window.location.href = "/groups/" + table.rows({selected: true}).data().toArray()[0].id + "/products";
						},
						enabled: false
					},

					@endif 'colvis',
				],
				ajax: '{!! route("groups.index") !!}',
				columns: [
					{data: 'name'},
					{data: 'customer_count'}

				]

				
			});

		 	@if(auth()->user()->hasPermission(auth()->user()->current_branch, 'write'))
			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		        table.button( 1 ).enable( selectedRows === 1 );
		        table.button( 2 ).enable( selectedRows === 1 );
		        table.button( 3 ).enable( selectedRows === 1 );
		    });
		    @endif

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

	</script>
@endsection