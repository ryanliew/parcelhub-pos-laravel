@extends('layouts.admin')

@section('page')
	Vendors
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Vendors</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="vendors-table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Formula</th>
							<th>Type</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<vendors-dialog></vendors-dialog>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#vendors-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [
					@if(auth()->user()->is_admin)
					{
						text: 'Create',
						action: function( e, dt, node, config ) {
							window.events.$emit('createVendor');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editVendor', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					@endif
					// Enable the following if delete is allowed
					// {
					// 	text: 'Delete',
					// 	action: function( e, dt, node, config ) {
					// 		window.events.$emit('deleteZone', table.rows({selected: true}).data().toArray());
					// 	},
					// 	enabled: false
					// },
					'excel', 'colvis'
				],
				ajax: '{!! route("vendors.index") !!}',
				columns: [
					{data: 'name'},
					{data: 'formula'},
					{data: 'zone_type.name'}
				]
			});

			@if(auth()->user()->is_admin)
			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 1 ).enable( selectedRows === 1 );
		        // Enable if delete is allowed
		        // table.button( 2 ).enable( selectedRows > 0 );
		    });
		    @endif

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

		
	</script>
@endsection