@extends('layouts.admin')

@section('page')
	Zones
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Zones</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="zones-table">
					<thead>
						<tr>
							<th>State</th>
							<th>Postcode Start</th>
							<th>Postcode End</th>
							<th>Zone</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<zones-dialog></zones-dialog>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#zones-table").DataTable({
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
							window.events.$emit('createZone');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editZone', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
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
				ajax: '{!! route("zones.index") !!}',
				columns: [
					{data: 'state'},
					{data: 'postcode_start'},
					{data: 'postcode_end'},
					{data: 'zone'}
				]
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 1 ).enable( selectedRows === 1 );
		        // Enable if delete is allowed
		        // table.button( 2 ).enable( selectedRows > 0 );
		    });

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

		
	</script>
@endsection