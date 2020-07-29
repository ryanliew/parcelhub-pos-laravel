@extends('layouts.admin')

@section('page')
	Stocks
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Stocks</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="stocks-table">
					<thead>
						<tr>
							<th>Date</th>
							<th>Quantity</th>
							<th>Type</th>
							<th>Active</th>
                            <th>Invoice</th>
							<th>Inventory</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<stocks-dialog></stocks-dialog>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script type="text/javascript" src="https://momentjs.com/downloads/moment.js"></script>
	<script>
		$(function(){
			var table = $("#stocks-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				//"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [
					@if(auth()->user()->is_admin)
					{
						text: 'Create',
						action: function( e, dt, node, config ) {
							window.events.$emit('createStock');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editStock', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					// Enable the following if delete is allowed
					{
						text: 'Delete',
						action: function( e, dt, node, config ) {
							window.events.$emit('deleteStock', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					@endif
					'excel', 'colvis'
				],
				ajax: '{!! route("stocks.index") !!}',
				columns: [
					{data: 'date', render: function(data, type, row){
						if(type === 'display' || type === 'filter') {
							return moment(data).format("YYYY-MM-DD");
						}
						return data;
					}, "searchable": false},
					{data: 'quantity', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data? parseFloat(data).toFixed(2) : 0;
							}

							return data;
						}, "searchable": false
					},
                    {data: 'type'},
                    {data: 'active', render: function(data,type,row){
							if(type === 'display' || type === 'filter') {
								return data ? "<i class='fas fa-check'></i>" : "<i class='fas fa-times'></i>";
							}

							return data;
						}
                    },
                    {data: 'invoice_no'},
					{data: 'inventory_name'},					
				]
			});

			@if(auth()->user()->is_admin)
			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 1 ).enable( selectedRows === 1 );
		        // Enable if delete is allowed
		        table.button( 2 ).enable( selectedRows > 0 );
		    });
		    @endif

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

		
	</script>
@endsection