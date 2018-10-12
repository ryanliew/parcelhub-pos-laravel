@extends('layouts.admin')

@section('page')
	Branches
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Branches</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="branches-table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Code</th>
							<th>Business Display Name</th>
							<th>Registered Business Name</th>
							<th>Contact</th>
							<th>Email</th>
							<th>Registration No.</th>
							<th>Payment Bank</th>
							<th>Payment Account</th>
							<th>GST No.</th>
							<th>Fax</th>
							<th>Toll Free</th>
							<th>Website</th>
							<th>Address</th>
							<th>Default product type</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<branches-dialog></branches-dialog>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#branches-table").dataTable({
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
							window.events.$emit('createBranch');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editBranch', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					'excel', 'colvis'
				],
				ajax: '{!! route("branches.index") !!}',
				columns: [
					{data: 'name'},
					{data: 'code'},
					{data: 'owner'},
					{data: 'registered_company_name'},
					{data: 'contact'},
					{data: 'email'},
					{data: 'registration_no'},
					{data: 'payment_bank'},
					{data: 'payment_acc_no'},
					{data: 'gst_no'},
					{data: 'fax'},
					{data: 'tollfree'},
					{data: 'website'},
					{data: 'address'},
					{data: 'default_product_type_name'}
				],
				
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		        table.button( 1 ).enable( selectedRows === 1 );
		        // table.button( 2 ).enable( selectedRows > 0 );
		    });
		    
		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

		
	</script>
@endsection