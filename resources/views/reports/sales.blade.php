@extends('layouts.admin')

@section('page')
	Sales report
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/rg-1.1.0/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
	@foreach($report_detail as $detail)
		<div class="card mt-5">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">	
						<h1>Sales report</h1>
						<p>
							<strong class="font-header">{{ $detail['branch']->owner }}</strong><br>
							<b>Co Reg No:</b> {{ $detail['branch']->registration_no }}
							<br>
							{{ $detail['branch']->address }}<br>
							<b>Phone:</b> {{ $detail['branch']->contact }}
							<br>
						</p>
					</div>
				</div>

				<div class="row">
					<div class="col-12 col-md-4 text-center">
						Period <br>
						<h4>{{ request()->from }} - {{ request()->to }}</h4>
					</div>
					<div class="col-12 col-md-4 text-center">
						Total sales <br>
						<h4>RM{{ number_format($detail['items']->sum('total_price_after_discount'), 2, ".", "") }}</h4>
					</div>
					<div class="col-12 col-md-4 text-center">
						Total items <br>
						<h4>{{ $detail['items']->count() }}</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<div class="d-flex align-items-center">
					<b class="flex-grow-1">Vendors sale ({{ request()->from }} - {{ request()->to }}) - {{ $detail['branch']->name }} - ( Total: RM{{number_format($detail['vendors_sum'], 2, ".", "") }} )</b>
					<a class="download-button" href="{{ url()->full() }}&export=true">Download</a>
				</div>

			</div>
			<div class="card-body">
				<table class="table table-bordered" id="vendors-table">
					<thead>
						<tr>
							<th>Vendor</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						@foreach($detail['vendors'] as $name => $vendor_item)
							<tr>
								<td>{{ $name }}</td>
								<td>RM{{ number_format($vendor_item->sum('total_price_after_discount'), 2, ".", "") }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<div class="card mt-5">
			<div class="card-header">
				<b>SKU types sale ({{ request()->from }} - {{ request()->to }}) - {{ $detail['branch']->name }} - ( Total: RM{{number_format($detail['skutypes_sum'], 2, ".", "") }} )</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="product-types-table">
					<thead>
						<tr>
							<th>SKU type</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						@foreach($detail['skutypes'] as $name => $skutype_item)
							<tr>
								<td>{{ $name }}</td>
								<td>RM{{ number_format($skutype_item->sum('total_price_after_discount'), 2, ".", "") }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<div class="card mt-5">
			<div class="card-header">
				<b>Sales by Product ({{ request()->from }} - {{ request()->to }}) - {{ $detail['branch']->name }}</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="products-table">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Description</th>
							<th>Zone</th>
							<th>Vendor</th>
							<th>Weight</th>
							<th>Type</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						@foreach($detail['products'] as $sku => $product)
							<tr>
								<td>{{ $sku }}</td>
								<td>{{ $product->first()->product->description }}</td>
								<td>{{ $product->first()->product->zone }}</td>
								<td>@if($product->first()->product->vendor_id) {{ $product->first()->product->vendor->name }} @else - @endif</td>
								<td>{{ $product->first()->product->weight_start }} - {{ $product->first()->product->weight_end }}</td>
								<td>{{ $product->first()->product->product_type->name }}</td>
								<td>RM{{ number_format($product->sum('total_price_after_discount'), 2, ".", "") }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<div class="card mt-5">
			<div class="card-header">
				<b>Detailed sales ({{ request()->from }} - {{ request()->to }}) - {{ $detail['branch']->name }}</b>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="items-table">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Description</th>
							<th>Zone</th>
							<th>Vendor</th>
							<th>Weight</th>
							<th>Actual</th>
							<th>Type</th>
							<th>Invoice</th>
							<th>Tracking no.</th>
							<th>Amount</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						@foreach($detail['items'] as $item)
							<tr>
								<td>{{ $item->product->sku }}</td>
								<td>{{ $item->product->description }}</td>
								<td>{{ $item->product->zone }}</td>
								<td>@if($item->product->vendor_id){{ $item->product->vendor->name }} @else - @endif</td>
								<td>{{ $item->product->weight_start }} - {{ $item->product->weight_end }}</td>
								<td>{{ $item->weight }}</td>
								<td>{{ $item->product->product_type->name }}</td>
								<td><a href="/invoices/edit/{{ $item->invoice->id }}" target="_blank">{{ $item->invoice->invoice_no }}</a></td>
								<td>{{ $item->tracking_code }}</td>
								<td>RM{{ number_format($item->total_price_after_discount, 2, ".", "") }}</td>
								<td>{{ $item->created_at->toDateString() }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
		@endforeach
		</div>
@endsection

@section('js')
	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.18/rg-1.1.0/datatables.min.js"></script>
	<script>
		$(function(){

			var table = $("#vendors-table").DataTable({
				paging: false,
				searching: false
			});

			var skutypeTable = $("#product-types-table").DataTable({
				paging: false,
				searching: false
			});

			var productsTable = $("#products-table").DataTable({
				paging: false,
				searching: false,
				order: [[5, 'asc'], [0, 'asc']],
				rowGroup: {
					startRender: function(rows, group) {
						var totalSales = rows.data().pluck(6).reduce( function (a, b) {
		                        return a + b.replace("RM", '')*1;
		                    }, 0);

		                totalSales = $.fn.dataTable.render.number(",", '.', 2, 'RM').display(totalSales);

		                return $('<tr/>')
		                	.append('<td colspan="6">' + group + "</td>")
		                	.append('<td>' + totalSales+"</td>");
					},
					endRender: null,
					dataSrc: 5
				}
			});

			var itemTable = $("#items-table").DataTable({
				paging: false,
				searching: false
			});
		});

		
	</script>
@endsection