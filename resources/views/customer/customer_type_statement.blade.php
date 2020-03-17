@extends('layouts.app')

@section('page')
	Customers
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Customer statements</b>
			</div>
			<div class="card-body">
                <div class="container-fluid">
					<customer-type-statements customer_type="{{request()->type}}" from="{{request()->from}}" to="{{request()->to}}"></customer-type-statements>
				</div>
				@if(request()->has('type'))
                <div class="mt-5" style="position:relative">
                    <table class="table table-bordered" id="customers-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Customer group</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Payment terms</th>
                                <th>Fax</th>
                                <th>Registration/IC No</th>
                                <th>Address Line 1</th>
                                <th>Address Line 2</th>
                                <th>Address Line 3</th>
                                <th>Address Line 4</th>
                                <th>Branch</th>
                            </tr>
                        </thead>
                    </table>
					<div id="processingIndicator"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>Loading...</div>
                </div>
				@endif
			</div>
		</div>
	</div>	
	<statement-dialog :data = "{{ auth()->user() }}" :is-multiple="true" from="{{request()->from}}" to="{{request()->to}}"></statement-dialog>
@endsection

@section('js')
 	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
	<script>
		$(function(){
			var table = $("#customers-table").DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				colReorder: true,
				language: {
					  processing: ' '
				},
				select: {
					style: 'multiple'
				},
				dom: 'Blftip',
				buttons: [
					{
						text: 'Generate statement',
						action: function( e, dt, node, config ) {
							window.events.$emit('generateStatement', table.rows({selected: true}).data().toArray());
						},
						enabled: false
					},
					'excel', 'colvis'
				],
				ajax: '{!! url()->full() !!}&data=true',
				columns: [
					{data: 'type'},
					{data: 'group_name', name: 'customer_group_id'},
					{data: 'name'},
					{data: 'contact'},
					{data: 'email', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'terms'},
					{data: 'fax', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'registration_no', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'address1', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'address2', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'address3', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'address4', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return data ? data : "---";
							}

							return data;
						}
					},
					{data: 'branch.name'} ,
				]
			});

			table.on( 'select deselect', function () {
		        var selectedRows = table.rows( { selected: true } ).count();
		 		
			    table.button( 0 ).enable( selectedRows > 0 );
		    });

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
			});
			
			table.on( 'processing.dt', function ( e, settings, processing ) {
				$('#processingIndicator').css( 'display', processing ? 'flex' : 'none' );
			});
		});
		
	</script>
@endsection