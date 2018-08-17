@extends('layouts.app')

@section('page')
	Invoices
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Invoices</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="branch-product-table">
					<thead>
						<tr>
							<th>Datetime</th>
							<th>Invoice No.</th>
							<th>Customer</th>
							<th>Subtotal</th>
							<th>Discount(RM)</th>
							<th>GST</th>
							<th>Total</th>
							<th>Payment</th>
							<th>Last update</th>
							<th>Outstanding</th>  
							<th>Remarks</th>
							<th>Tracking codes</th>
							<!-- <th>Payment</th>   -->
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
			var table = $("#branch-product-table").DataTable({
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
						text: 'View',
						// text: 'Edit',
						action: function( e, dt, node, config ) {
							location.href = "/invoices/edit/" + table.rows({selected: true}).data().toArray()[0].id;
						},
						enabled: false
					},
					'excel', 'colvis',
				],
				ajax: '{!! route("invoices.index") !!}',
				columns: [
					{data: 'created_at'},
					{data: 'invoice_no'},
					{data: 'customer', name:'customer.name'},
					{data: 'subtotal', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
					{data: 'discount', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
					{data: 'tax', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
					{data: 'total', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
					{data: 'payment', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
					{data: 'updated_at'},
					{data: 'outstanding', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
					{data: 'remarks', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'tracking_codes', name: 'items.tracking_code'}
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