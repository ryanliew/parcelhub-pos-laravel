@extends('layouts.admin')

@section('page')
	Sales report
@endsection

@section('styles')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/rg-1.1.0/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<h1>Sales report</h1>
						<p>
							<strong class="font-header">{{ $branch->owner }}</strong><br>
							<b>Co Reg No:</b> {{ $branch->registration_no }}
							<br>
							{{ $branch->address }}<br>
							<b>Phone:</b> {{ $branch->contact }}
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
						<h4>RM{{ number_format($items->sum('total_price'), 2, ".", "") }}</h4>
					</div>
					<div class="col-12 col-md-4 text-center">
						Total items <br>
						<h4>{{ $items->count() }}</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<b>Vendors sale ({{ request()->from }} - {{ request()->to }}) - {{ $branch->name }}</b>
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
						@foreach($vendors as $name => $vendor_item)
							<tr>
								<td>{{ $name }}</td>
								<td>RM{{ number_format($vendor_item->sum('total_price'), 2, ".", "") }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<div class="card mt-5">
			<div class="card-header">
				<b>Sales by Product ({{ request()->from }} - {{ request()->to }}) - {{ $branch->name }}</b>
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
						@foreach($products as $sku => $product)
							<tr>
								<td>{{ $sku }}</td>
								<td>{{ $product->first()->product->description }}</td>
								<td>{{ $product->first()->product->zone }}</td>
								<td>@if($product->first()->product->vendor_id) {{ $product->first()->product->vendor->name }} @else - @endif</td>
								<td>{{ $product->first()->product->weight_start }} - {{ $product->first()->product->weight_end }}</td>
								<td>{{ $product->first()->product->product_type->name }}</td>
								<td>RM{{ number_format($product->sum('total_price'), 2, ".", "") }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<div class="card mt-5">
			<div class="card-header">
				<b>Detailed sales ({{ request()->from }} - {{ request()->to }}) - {{ $branch->name }}</b>
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
							<th>Type</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						@foreach($items as $item)
							<tr>
								<td>{{ $item->product->sku }}</td>
								<td>{{ $item->product->description }}</td>
								<td>{{ $item->product->zone }}</td>
								<td>@if($item->product->vendor_id){{ $item->product->vendor->name }} @else - @endif</td>
								<td>{{ $item->product->weight_start }} - {{ $item->product->weight_end }}</td>
								<td>{{ $item->product->product_type->name }}</td>
								<td>RM{{ number_format($item->total_price, 2, ".", "") }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
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

			var productsTable = $("#products-table").DataTable({
				paging: false,
				searching: false,
				order: [[5, 'asc']],
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