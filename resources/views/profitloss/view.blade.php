@extends('layouts.app')

@section('page')
	ProfitAndLoss
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Profit and Loss</b>
			</div>
			<div class="card-body">
				<div class="container-fluid">
					<profit-and-loss-import created_by="{{ auth()->id() }}"></profit-and-loss-import>
				</div>
				<div class="mt-5">
					<table class="table table-bordered" id="profit-and-losses-table">
						<thead>
							<tr>
								<th>Tracking code</th>
								<th>Cost Price</th>
								<th>Sell Price</th>
								<th>Margin (%)</th>
								<th>Profit</th>
							</tr>
						</thead>
					</table> 
					<div id="processingIndicator"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>Loading...</div>
				</div>
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
			var table = $("#profit-and-losses-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [
					{
						extend: 'excel',
						text: function ( dt, button, config ) {
							return dt.i18n( 'buttons.excel', 'Generate excel' );
						}
					},
					'colvis'
				],
				ajax: '{!! route("profit-and-loss.index") !!}',
				columns: [
					{data: 'tracking_code'},
					{data: 'sales'},
					{data: 'price'},
					{data: 'margin'},					
					{data: 'profit'} ,
				],
				"order": [[ 0, "asc" ]],

				"rowCallback": function( row, data, index ) {
					var allData = this.api().column(0).data().toArray();
					var num = 0;
					allData.forEach( function (trackingcode) {
					if(trackingcode == data.tracking_code){
							num ++;
						}
					})
					if(num>1){
						$('td', row).css('background-color', 'Yellow');
					}
				}
			});

			// table.on( 'select deselect', function () {
		    //     var selectedRows = table.rows( { selected: true } ).count();
		 	// 	@if(auth()->user()->hasPermission(auth()->user()->current_branch, 'write'))
			//         table.button( 1 ).enable( selectedRows === 1 );
			//         table.button( 2 ).enable( selectedRows > 0 );
			//     @else
			//     		table.button( 0 ).enable( selectedRows > 0 );
			//     @endif
			// });

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });

			table.on( 'processing.dt', function ( e, settings, processing ) {
				$('#processingIndicator').css( 'display', processing ? 'flex' : 'none' );
			});
		});

		
	</script>
@endsection