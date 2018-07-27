@extends('layouts.app')

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Payments</b> 

				@if( $invoice_no !== '' )

				<b>per Invoice : {{$invoice_no}}</b>

				@endif
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="payments-table">
					<thead>
						<tr>
							<th>Datetime</th>
							<th>Customer</th>
							<th>Branch</th>
							<th>Terminal</th>
							<th>Total</th>
							<th>Payment method</th>
							<th>Invoice No.</th>
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
			var url = '/payments/index/{{ $invoice_no }}';//: '{!! route("payments.index") !!}';
			var table = $("#payments-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [
					// {
					// 	text: 'Create',
					// 	action: function( e, dt, node, config ) {
					// 		window.events.$emit('createPayment');
					// 	}
					// },
					// {
					// 	text: 'Edit',
					// 	action: function( e, dt, node, config ) {
					// 		window.events.$emit('editPayment', table.rows({selected: true}).data().toArray());
					// 	},
					// 	enabled: false
					// },
					'excel', 'colvis'
				],
				ajax: url,
				columns: [
					{data: 'updated_at'},
					{data: 'customer', name:'customer.name'},
					{data: 'branch', name:'branch.name'},
					{data: 'terminal_no' },
					{data: 'total', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}	
					},
					{data: 'payment_method'},
					{data: 'invoice_no'},
				]
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 1 ).enable( selectedRows === 1 );
		        table.button( 2 ).enable( selectedRows > 0 );
		    });

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

        
		
	</script>
@endsection