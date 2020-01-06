@extends('layouts.admin')

@section('page')
	Items
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">        
		<div class="card">
			<div class="card-header">
				<b>Global consignment notes</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="items-table">
                    <thead>
						<tr>
                            <th>Branch</th>
                            <th>Terminal</th>
                            <th>Invoice no</th>
                            <th>Tracking code</th>
                            <th>Product type</th>
                            <th>Product id</th>
                            <th>Zone type</th>
							<th>Zone</th>
                            <th>SKU</th>
							<th>Description</th>
							<th>Price</th>
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
			var table = $("#items-table").DataTable({
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
						text: 'View',
						action: function( e, dt, node, config ) {
                            var loc = "/invoices/edit/" + table.rows({selected: true}).data().toArray()[0].invoice_id;

							window.open(loc, '_blank');
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
				ajax: '{!! route("items.index") !!}',
				columns: [
                    {data: 'branch'}, 
                    {data: 'terminal'}, 
					{data: 'invoice_no'}, 
                    {data: 'tracking_code'},
                    {data: 'product_type'},
                    {data: 'product_id'}, 
                    {data: 'zone_type'},
					{data: 'zone'},
                    {data: 'sku'},
                    {data: 'description'},
                    {data: 'price'}
				]
            });
            
			@if(auth()->user()->is_admin)
			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 0 ).enable( selectedRows === 1 );
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