@extends('layouts.app')

@section('page')
	Parcels
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Check in parcels</b>
			</div>
			<div class="card-body">
				<div class="container-fluid">                
                    <parcels-check-in created_by="{{ auth()->id() }}"></parcels-check-in>
				</div>
                <div class="mt-5">
					<table class="table table-bordered" id="parcels-table">
						<thead>
							<tr>
								<th>Tracking code</th>
                                <th>Phone number</th>
								<th>Status</th>
								<th>Charge(RM)</th>
							</tr>
						</thead>
					</table> 
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
			var table = $("#parcels-table").DataTable({
				processing: true,
				serverSide: false,
				responsive: true,
				colReorder: true,
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [
					'excel', 'colvis'
				],
				ajax: '{!! route("parcels.index") !!}',
				columns: [
                    {data: 'tracking_code'},
                    {data: 'phone'},
					{data: 'status'},
					{data: 'charge'},
				]
			});
		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});
	</script>
@endsection