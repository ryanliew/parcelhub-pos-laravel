@extends('layouts.admin')

@section('page')
	SKU
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>SKU</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="products-table">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Product type</th>
							<th>Vendor</th>
							<th>Zone</th>
							<th>Zone type</th>
							<th>Weight start</th>
							<th>Weight end</th>
							<th>Corporate price</th>
							<th>Walk in price</th>
							<th>Walk in price special</th>
							<th>Tax</th>
							<th>Tax inclusive</th>
							<th>Description</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<products-dialog></products-dialog>

		<products-importer></products-importer>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#products-table").DataTable({
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
						text: 'Create',
						action: function( e, dt, node, config ) {
							window.events.$emit('createProduct');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editProduct', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					{
						text: "Import",
						action: function( e, dt, node, config ){
							window.events.$emit("importProducts");
						}
					},
					@endif
					// Enable the following if delete is allowed
					// {
					// 	text: 'Delete',
					// 	action: function( e, dt, node, config ) {
					// 		window.events.$emit('deleteProduct', table.rows({selected: true}).data().toArray());
					// 	},
					// 	enabled: false
					// },
					'excel', 'colvis'
				],
				ajax: '{!! route("products.index") !!}',
				columns: [
					{data: 'sku'},
					{data: 'product_type_name', name: 'product_type.name'},
					{data: 'vendor_name', name: 'vendor.name'},
					{data: 'zone'},
					{data: 'zone_type_name', name: 'zone_type.name'},
					{data: 'weight_start'},
					{data: 'weight_end'},
					{data: 'corporate_price'},
					{data: 'walk_in_price'},
					{data: 'walk_in_price_special'},
					{data: 'tax_code', name: 'tax.code'},
					{data: 'is_tax_inclusive', render: function(data,type,row){
							if(type === 'display' || type === 'filter') {
								return data ? "<i class='fas fa-check'></i>" : "<i class='fas fa-times'></i>";
							}

							return data;
						}
					},
					{data: 'description'}
					
				]
			});

			@if(auth()->user()->is_admin)
			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 1 ).enable( selectedRows === 1 );
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