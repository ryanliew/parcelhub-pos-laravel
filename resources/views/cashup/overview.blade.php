@extends('layouts.app')

@section('page')
	Cashup
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Cashups</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="cashup-table">
					<thead>
						<tr>
							<th>Session Time</th>
							<th>Create Time</th>
							<th>Terminal</th>
							<th>Invoice From</th>
							<th>Invoice To</th>
							<th>Total(RM)</th>
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
	<script type="text/javascript" src="https://momentjs.com/downloads/moment.js"></script>
	<script>
		$(function(){
			var table = $("#cashup-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				order: [1, 'desc'],
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [
					{
						text: 'Draft report',
						action: function( e, dt, node, config ) {
							swalalert("Are you sure?", "", 'warning', function() {
								axios.post("{!! route('cashups.page') !!}")
									.then(response => onSuccess(response));
							});
						}
					},
					{
						text: 'View report',
						action: function( e, dt, node, config ) {
							
							window.open("/cashups/view/" + table.rows({selected: true}).data().toArray()[0].id);
						},
						enabled: false
					},
					'excel', 'colvis'
				],
				ajax: '{!! route("cashups.index") !!}',
				columns: [
					{data: 'session_start', render: function(data, type, row){
						if(type === 'display' || type === 'filter') {
							return moment(data).format("YYYY-MM-DD");
						}

						return data;
					}},
					{data: 'created_at', render: function(data, type, row){
						if(type === 'display' || type === 'filter') {
							return moment(data).format("YYYY-MM-DD");
						}

						return data;
					}, "searchable": false},
					{data: 'terminal_name', name:'terminal.name'},
					{data: 'invoice_from'},
					{data: 'invoice_to'},
					{data: 'total', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
				]

				
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 1 ).enable( selectedRows === 1 );
		        table.button( 0 ).enable( selectedRows === 0 );
		    });

			function onSuccess(response) {
				flash(response.data.message);
				if(response.data.id)
					window.open("/cashups/view/" + response.data.id);
				table.ajax.reload();
			}

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

	</script>
@endsection