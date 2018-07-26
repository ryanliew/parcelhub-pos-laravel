@extends('layouts.admin')


@section('page')
	SKU Types
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>SKU Types</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="product-types-table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Is merchandise</th>
							<th>Is document</th>
							<th>Has detail</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

		<product-types-dialog></product-types-dialog>
	</div>

@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#product-types-table").DataTable({
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
							window.events.$emit('createProductType');
						}
					},
					{
						text: 'Edit',
						action: function( e, dt, node, config ) {
							window.events.$emit('editProductType', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					// Enable the following if delete is allowed
					// {
					// 	text: 'Delete',
					// 	action: function( e, dt, node, config ) {
					// 		window.events.$emit('deleteProductType', table.rows({selected: true}).data().toArray());
					// 	},
					// 	enabled: false
					// },
					'excel', 'colvis'
				],
				ajax: '{!! route("product-types.index") !!}',
				columns: [
					{data: 'name'},
					{data: 'is_merchandise', render: function(data,type,row){
							if(type === 'display' || type === 'filter') {
								return data ? "<i class='fas fa-check'></i>" : "<i class='fas fa-times'></i>";
							}

							return data;
						}
					},
					{data: 'is_document', render: function(data,type,row){
							if(type === 'display' || type === 'filter') {
								return data ? "<i class='fas fa-check'></i>" : "<i class='fas fa-times'></i>";
							}

							return data;
						}
					},
					{data: 'has_detail', render: function(data,type,row){
							if(type === 'display' || type === 'filter') {
								return data ? "<i class='fas fa-check'></i>" : "<i class='fas fa-times'></i>";
							}

							return data;
						}
					}
				],
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 
		        table.button( 1 ).enable( selectedRows === 1 );
		        // Enable if delete is allowed
		        // table.button( 2 ).enable( selectedRows > 0 );
		    });

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });
		});

		
	</script>
@endsection