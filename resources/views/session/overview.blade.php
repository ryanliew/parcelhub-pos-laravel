@extends('layouts.app')

@section('page')
	Sessions
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Sessions</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="sessions-table">
					<thead>
						<tr>
							<th>Table</th>
							<th>Activated at</th>
							<th>Deactivated at</th>
							<th>Discount</th>
							<th>Total</th>
							<th>Paid</th>
							<th>Payment type</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#sessions-table").DataTable({
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
						text: 'View details',
						action: function( e, dt, node, config ) {
							window.location.href = "/sessions/" + table.rows({selected: true}).data().toArray()[0].id + "/view";
						},
						enabled: false
					},
					@endif
					'excel', 'colvis'
				],
				ajax: '{!! route("sessions.index") !!}',
				columns: [
					{data: 'table_name'},
					{data: 'activated_at'},
					{data: 'deactivated_at'},
					{data: 'discount_value', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}, "searchable": false
					},
					{data: 'total', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}, "searchable": false
					},
					{data: 'paid', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}, "searchable": false
					},
					{data: 'payment_type'},
				]
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
			    table.button( 0 ).enable( selectedRows === 1 );

		    });

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

		
	</script>
@endsection