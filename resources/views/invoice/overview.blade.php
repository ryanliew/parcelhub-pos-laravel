@extends('layouts.app')

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
				<table class="table table-bordered" id="invoice-table">
					<thead>
						<tr>
							<th>Invoice No.</th>
							<th>Customer</th>
							<th>Remarks</th>
							<th>Subtotal</th>
							<th>Discount(RM)</th>
							<th>GST</th>
							<th>Total</th>
							<th>Last update</th>
							<th>Terminal</th>
							<th>Outstanding</th>  
							<!-- <th>Payment</th>   -->
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<payments-dialog></payments-dialog>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#invoice-table").DataTable({
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
							window.events.$emit('createPayment');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editPayment', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					'excel', 'colvis',
					{
						text: 'Add payment',
						action: function( e, dt, node, config ) {
							window.events.$emit('createPayment', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
				],
				ajax: '{!! route("invoices.index") !!}',
				columns: [
					{data: 'id'},
					{data: 'customer.name'},
					{data: 'remarks'},
					{data: 'subtotal'},
					{data: 'discount'},
					{data: 'tax'},
					{data: 'total'},
					{data: 'updated_at'},
					{data: 'terminal_no'},
					{data: 'outstanding'},
				]

				
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 1 ).enable( selectedRows === 1 );
		        table.button( 4 ).enable( selectedRows === 1 );
		        table.button( 2 ).enable( selectedRows > 0 );
		    });

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

	    $(document).ready(function() {
		    $('#invoice-table tbody').on( 'click', 'button', function () {
		        window.events.$emit('createPayment');
		    } );
		} );

	</script>
@endsection