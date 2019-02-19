@extends('layouts.app')

@section('page')
	SKU for {{ $group->name }} group
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>SKU for {{ $group->name }} group</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="groups-table">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Price</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<groups-product-dialog :user="{{ auth()->user() }}" :group="{{ $group }}"></groups-product-dialog>

		<products-importer url='/groups/{{ $group->id }}/products/import'></products-importer>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#groups-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				select: {
					style: 'single'
				},
				dom: 'Blftip',
				buttons: [

		 			@if(auth()->user()->hasPermission(auth()->user()->current_branch, 'write'))
					{
						text: 'Create',
						action: function( e, dt, node, config ) {
							window.events.$emit('createCustomerGroupProduct');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editCustomerGroupProduct', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					// Enable the following if delete is allowed
					{
						text: 'Delete',
						action: function( e, dt, node, config ) {
							swalalert("Are you sure?", "", 'warning', function(){
								var item = table.rows({selected: true}).data().toArray()[0];
						        
								axios.delete("/groups/" + {{ $group->id }} + "/products/" + item.id )
									.then(response => window.events.$emit('deletedCustomerGroupProduct', response));
							});
						},
						enabled: false
					},

					{
						text: "Import",
						action: function( e, dt, node, config ){
							window.events.$emit("importProducts");
						}
					},

					@endif 'excel', 'colvis',
				],
				ajax: '{!! route("groups.products", ['group' => $group->id]) !!}',
				columns: [
					{data: 'sku'},
					{data: 'corporate_price', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					}

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