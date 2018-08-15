@extends('layouts.app')

@section('page')
	Branch Product
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Branch products</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="branch-product-table">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Customer</th>
							<th>Courier</th>
							<th>Walk In Price</th>
							<th>Walk In Override</th>
							<th>Walk In Special Price</th>  
							<th>Walk In Special Override</th>
							<th>Corporate Price</th>
							<th>Corporate Override</th>
							<th>Last update time</th>
							<th>GST Inclusive</th>
							<th>Zone</th>
							<th>Weight Start(Kg)</th>
							<th>Weight End(Kg)</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<pricing-dialog :data = "{{ auth()->user() }}"></pricing-dialog>
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

		 			@if(auth()->user()->hasPermission(auth()->user()->current_branch, 'write'))
					{
						text: 'Create',
						action: function( e, dt, node, config ) {
							window.events.$emit('createBranchProduct');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editBranchProduct', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					// Enable the following if delete is allowed
					{
						text: 'Delete',
						action: function( e, dt, node, config ) {
							swalalert("Are you sure?", "", 'warning', function(){
								var item = table.rows({selected: true}).data().toArray()[0];
								// console.log(item);
								var formData = new FormData();

								var requestString = item.pivot.customer_id ? "?customer=" + item.pivot.customer_id : "";

						        formData.append('customer_id', item.pivot.customer_id);
						        
								axios.delete("/branch/product/" + item.id + requestString)
									.then(response => table.ajax.reload());
							});
						},
						enabled: false
					},
					@endif
					'excel', 'colvis',
				],
				ajax: '{!! route("branch-product.index") !!}',
				columns: [
					{data: 'sku'},
					{data: 'customer_name'},
					{data: 'courier_name'},
					{data: 'walk_in_price'},
					{data: 'pivot.walk_in_price'},
					{data: 'walk_in_price_special'},
					{data: 'pivot.walk_in_price_special'},
					{data: 'corporate_price'},
					{data: 'pivot.corporate_price'},
					{data: 'last_update'},
					{data: 'is_tax_inclusive', render: function(data,type,row){
							if(type === 'display' || type === 'filter') {
								return data ? "<i class='fas fa-check'></i>" : "<i class='fas fa-times'></i>";
							}

							return data;
						}
					},
					{data: 'zone'},
					{data: 'weight_start'},
					{data: 'weight_end'}

				]

				
			});

		 	@if(auth()->user()->hasPermission(auth()->user()->current_branch, 'write'))
			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		        table.button( 1 ).enable( selectedRows === 1 );
		        table.button( 2 ).enable( selectedRows === 1 );
		    });
		    @endif

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

	</script>
@endsection