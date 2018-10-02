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
							<th>Date</th>
							<th>Invoice No.</th>
							<th>Customer type</th>
							<th>Payment type</th>
							<th>Customer</th>
							<th>Subtotal(RM)</th>
							<th>Discount(RM)</th>
							<th>GST(RM)</th>
							<th>Total(RM)</th>
							<th>Payment(RM)</th>
							<th>Outstanding(RM)</th>  
							<th>Last update</th>
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
	<script type="text/javascript" src="https://momentjs.com/downloads/moment.js"></script>
	<script>
		$(function(){
			var table = $("#branch-product-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				order: [0, 'desc'],
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
					{data: 'created_at', render: function(data, type, row){
						if(type === 'display' || type === 'filter') {
							return moment(data).format("YYYY-MM-DD");
						}

						return data;
					}, "searchable": false},
					{data: 'invoice_no'},
					{data: 'type'},
					{data: 'payment_type'},
					{data: 'customer', name:'customer.name'},
					{data: 'subtotal', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}, "searchable": false
					},
					{data: 'discount_value', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}, "searchable": false
					},
					{data: 'tax', render: function(data, type, row){
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
					{data: 'payment', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}, "searchable": false
					},
					{data: 'outstanding', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}, "searchable": false
					},
					{data: 'updated_at', render: function(data, type, row){
						if(type === 'display' || type === 'filter') {
							return moment(data).format("YYYY-MM-DD");
						}

						return data;
					}, "searchable": false},
					{data: 'remarks', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'tracking_codes', name: 'items.tracking_code', "searchable": false}
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