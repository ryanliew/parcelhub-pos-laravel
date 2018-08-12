@extends('layouts.app')

@section('page')
	Payment receive
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/r-2.2.2/sl-1.2.6/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<b>Payments receive</b> 
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-2">
						Customer <br>
						<select id='customer_name' class='form-control'>
							@foreach($customers as $customer)
								<option value='{{ $customer->name }}'>{{$customer->name}}</option>
							@endforeach 
						</select>
						<!-- Customer <input class='form-control' id='customer_name' type="text"><br> -->
					</div>
					<div class="col-2">
						Date from <input class='form-control' id='min' type="date" value="<?php echo date('Y-m-01');?>"><br>
					</div>
					<div class="col-2">
						Date to <input class='form-control' id= 'max' type="date" value="<?php echo date("Y-m-t");?>">
					</div>
					<div class="col-2">
						Options <br>
						<select name='Options' class='form-control' id='option'>
								<option value="All">All</option>
  								<option value="Unpaid">Unpaid</option>
						</select>
					</div>
					<div class="d-flex align-items-center" style="margin-left: 10px">
						<button class='btn btn-primary align-item-bottom' id='btnSearch'>Search</button>
					</div>
				</div>
				<table class="table table-bordered" id="payments-table">
					<thead>
						<tr>
							<th>Datetime</th>
							<th>Invoice No.</th>
							<th>Customer</th>
							<th>Total</th>
							<th>Paid amount</th>
							<th>Outstanding</th>  
							<th>Amt to pay</th>
						</tr>
					</thead>
					<tfoot>
			            <tr>
			                <th colspan="6" style="text-align:right">Total:</th>

			                <th></th>
			            </tr>
			            <tr>
			                <th colspan="7" >
			                	<div class="d-flex align-items-center" style="margin-left: 10px; float: right;"> Payment type: <div>
			                	<div class="d-flex align-items-center" style="margin-left: 10px; float: right;">
				                		<select name='Options' class='form-control flex:1' id='payment_type' style="width: 100px" >
												<option value="Cash">Cash</option>
				  								<option value="Cheque">Cheque</option>
				  								<option value="Credit card">Credit card</option>
				  								<option value="IBG">IBG</option>
										</select>
										<span style="min-width: 20px;"></span>
				            		<button class='btn btn-primary' id='btnPayment'>Make payments</button>
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
	<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
	<script>
		$(function(){
			var totalAMT = 0.0;
			var table = $("#payments-table").DataTable({
				bFilter: true,
				dom: 'Bt',
				buttons:[],
				columnDefs: [ {
		            "targets": -1,
		            "data": null,
		            "defaultContent": "<input class='form-control' type='number' step='.01' value=0.00 ></input>",
		        } ],
				columns: [
					{data: 'created_at'},
					{data: 'invoice_no'},
					{data: 'customer', name:'customer.name', searchable: true },
					{data: 'total', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
					{data: 'payment', render: function(data, type, row){
							if(type === 'display' || type === 'filter') {
								return parseFloat(data).toFixed(2);
							}

							return data;
						}
					},
					{data: 'outstanding', render: function(data, type, row){
							if(type === 'display') {
								return "<button class='btn btn-primary'>"+parseFloat(data).toFixed(2)+"</button>";//;
							}

							return data;
						},
					},
					{data: ''},
					
				],

				footerCallback: function ( row, data, start, end, display ) {
			            var api = this.api(), data;
			 
			            // Remove the formatting to get integer data for summation
			            var intVal = function ( i ) {
			                return typeof i === 'string' ?
			                    i.replace(/[\$,]/g, '')*1 :
			                    typeof i === 'number' ?
			                        i : 0;
			            };
			 
			            // Update footer
			            $( api.column( 6 ).footer() ).html(
			                'RM '+ parseFloat(totalAMT).toFixed(2)
			            );
			        },
			});

			$('#payments-table tbody').on( 'change', 'input', function () {
		        sumTotalAmt();
		    } );

		    $('#payments-table tbody').on('click', 'button', function () {
				 var data = table.cell( $(this).closest('tr'), 5 ).data();
				 
				 data = parseFloat(data).toFixed(2);	

				 var innerHtml = "<input class='form-control' type='number' step='.01' value="+data+" ></input>"

				 table.cell( $(this).closest('tr'), 6 ).data( innerHtml );

				 sumTotalAmt();

				} );

		    window.events.$on("reload-table", function(){
		    	table.ajax.reload();
		    });

		    var totalPaid = 0.0;

		    function sumTotalAmt()
		    {
		    	var totalAMT = 0.0;

			    $( table.column( 6).nodes() ).find( 'input' ).each( function () {
			      totalAMT += this.value.replace( ',', '' )*1;
			    } );

			   // Update footer
	            $( table.column( 6 ).footer() ).html(
	                'RM '+ parseFloat(totalAMT).toFixed(2)
	            );

	            return totalAMT;
		    }

	  		$.fn.dataTable.ext.search.push(
			    function( settings, data, dataIndex ) {
			    	var value = false;
			    	var min  = $('#min').val();
			        var max  = $('#max').val();
			        var option  = $('#option').val();
			        var createdAt = data[0] || 0; 
			        var outstanding = data[5] || 0;

			        if( ( min == "" || max == "" )
                		|| 	
                		( createdAt <= max && createdAt >= min) )
			        {
			        	value = true;
			        }

			        if( option == 'Unpaid' )
			        {
			        	value = value && outstanding > 0 
			        }

			        return value;

			    }
			);

			$("#btnSearch").click(function() {
		    	

		    	var customerInput = document.getElementById("customer_name");
		    	customer = customerInput.value;

		    	if(customer == '')
		    	{
		    		customerInput.setCustomValidity("Customer field is required");
		    		customerInput.reportValidity();

		    		flash("The given input is invalid",'danger');
		    	}
		    	else
		    	{
		    		table.ajax.url('{!! route("invoices.index") !!}').load();

		    		table
	   				.columns( 2 ).search( customer, true, false, true)
	   				.draw();
		    	}

	  		});

	  		$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});

	  		$("#btnPayment").click(function() {

	  			var row = table.rows({ filter : 'applied'});
	  			var amount_paid = sumTotalAmt();

	  			

	  			if( row.count() == 0 )
	  			{
	  				var search = document.getElementById("btnSearch");

	  				search.setCustomValidity("Please retrieve customer invoice(s)");
		    		search.reportValidity();

		    		flash("No invoices selected",'danger');
	  			}
	  			else if( amount_paid == 0)
	  			{
		    		flash("Total amount cannot be zero",'danger');
	  			}
	  			else
	  			{
	  				swalalert("Confirmation","Confirm payment amount of RM " + parseFloat(amount_paid).toFixed(2), 'warning', function() { 

		  				var type = document.getElementById("payment_type").value;
			  			var customer = row.data()[0]['customer_id'];

			  			var array = [];

			  			var detail_array = [];

			  			array.push(customer);
					    array.push(amount_paid);
					    array.push(type);

				    	table.rows({ filter : 'applied'}).every( function ( rowIdx, tableLoop, rowLoop ) {
						    var invoice = this.data()['invoice_no'];
						    var outstanding = this.data()['outstanding'];
						    var total = this.data()['total'];
						    var amount = this.nodes().to$().find('input').val();
						    var inner_array = [];

						    inner_array.push(invoice);
						    inner_array.push(amount);
						    inner_array.push(outstanding-amount);
						    inner_array.push(total);

						    detail_array.push(inner_array);

						} );

						array.push(detail_array);

			  			data = JSON.stringify( array );

				    	$.ajax({
						  type: "POST",
						  "_token": "{{ csrf_token() }}",
						  url: "/payments/create/",
						  data: data,
						  success: function(response){ 
						  	window.location.href ='/payments/detail/' + response.payment_id;
						  },
						  dataType: "json",
						  contentType : "application/json"
						});

				    });
	  			}
		    });

		});

	</script>
@endsection

