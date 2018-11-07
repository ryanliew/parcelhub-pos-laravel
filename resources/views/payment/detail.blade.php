@extends('layouts.app')

@section('page')
	Payment details
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Payments per invoice</b> <br><br>
				<b>Payment reference :</b> {{ $payment_ref }} <br>
				<b>Customer name :</b> {{ $customer }} <br>
				<b>Payment date :</b> {{ $payment_date }} <br>
				<b>Payment type :</b> {{ $payment_type }} <br>
				<b>Terminal	:</b> {{ $terminal }} <br>
			</div>
			
			<div class="card-body">
				<table class="table table-bordered" id="payments-table">
					<thead>
						<tr>
							<th>Invoice date</th>
							<th>Invoice no.</th>
							<th>Total</th>
							<th>Paid amount</th>
							<th>Outstanding</th>
							<th>Amt paid</th>
						</tr>
					</thead>
					<tfoot>
			            <tr>
			                <th colspan="5" style="text-align:right">Total:</th>
			                <th>RM {{ number_format((float)$total, 2, '.', '') }}</th>
			            </tr>
						<tr>
							<th colspan="6">
								<div style="float: right">
									<button class='btn btn-primary' id='btnBack'>Back to summary</button>
								</div>
							</th>
						</tr>
			        </tfoot>
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
			var url = '/payments/details/{{ $payment_id }}';
			var table = $("#payments-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				select: {
					style: 'single'
				},
				dom: 'Bt',
				buttons: [
					{
						text: 'Print',
						action: function( e, dt, node, config ) {

							window.open("/payments/receipt/{{ $payment_id }}");
						},
					},
					 'excel', 'colvis'
				],
				ajax: url,
				columns: [
					{data: 'invoice_date'},
					{data: 'invoice_no'},
					{data: 'invoice_total', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}	
					},
					{data: 'paid', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}	
					},
					{data: 'outstanding', render: function(data, type, row){
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
				]
			});
			

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

		$("#btnBack").click(function(){
			window.location.href = "/payments/all";
		});

        
		
	</script>
@endsection