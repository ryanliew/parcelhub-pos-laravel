@extends('layouts.app')

@section('page')
	Cashup details
@endsection

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">

				<h3><b>Cashup preview</b></h3>
				<div class="row align-items-end">
					<div class="col">
						<b>Drawer :</b> {{ $cashup->terminal->name }} <br>
						<b>Transactions :</b> {{ $cashup->invoice_from }} - {{ $cashup->invoice_to }}<br>
						<b>Session opened :</b> {{ $cashup->session_start->toDayDateTimeString() }} <br>
						<b>Report run :</b> {{ $cashup->created_at->toDayDateTimeString() }} <br>
						<b>Created by :</b> {{ $cashup->creator->name }} <br>
						<b>Status: </b>{{ ucfirst($cashup->status) }}
					</div>
					<div class="col">
						<cashup-status :cashup="{{ $cashup }}"></cashup-status>
						
					</div>
				</div>
			</div>
			
			<div class="card-body">
				<h3><b>Summary</b></h3>
				<table class="table" id="items">
				  	<tbody>
					  	<tr>
					   		<th>Legend</th>
						    <th>Payment Type</th>
					      	<th>Expected RM</th>
					      	<th>%</th>
					      	<th>Count</th>
					  	</tr>
					  	@foreach($cashup->invoices->groupBy(function($item){ return $item->pivot->payment_method; }) as $type => $records)
						  	<?php $sum = $records->sum(function($invoice){ return $invoice->pivot->total; }); ?>
					  		@if($sum > 0)
							  	<tr class="item-row">
							  		<td>
							  			@if($type == 'IBG')17
							  			@elseif($type == 'Cash')01
							  			@elseif($type == 'Credit card')02
							  			@elseif($type == 'Cheque')05
							  			@endif
							  		</td>
							  		<td>{{ $type }}</td>
							  		<td>{{ number_format( $sum, 2, ".", ",") }}</td>
							  		<td>{{ $cashup->total > 0 ? number_format( $sum / $cashup->total * 100, 2, ".", ",") : 0.00 }}</td>
							  		<td>{{ $records->count() }}</td>
							  	</tr>
						  	@endif
						@endforeach

					  	<tr class="item-row">
					  		<td>00</td>
					  		<td>Float</td>
					  		<td>{{ number_format( $cashup->float_value, 2, ".", ",") }}</td>
					  		<td>{{ $cashup->total > 0 ? number_format($cashup->float_value / $cashup->total * 100, 2, ".", ",") : 0.00}}</td>
					  		<td></td>
					  	</tr>

					</tbody>
					<tfoot>
						<tr>
							<td></td>
							<td><b>Total</b></td>
							<td><b>{{ number_format($cashup->total, 2, ".", ",") }}</b></td>
							<td></td>
							<td></td>
						</tr>
					</tfoot>
				</table> 


				<h3><b>Including invoices</b></h3>
				<table class="table" id="invoices">
			  	<tbody>
				  	<tr>
				   		<th>Invoice no</th>
					    <th>Payment Type</th>
				      	<th>Expected RM</th>
				      	<th>%</th>
				      	<th>Payment #</th>
				  	</tr>
				  	@foreach($cashup->invoices as $invoice)
				  		@if($invoice->pivot->total > 0)
						  	<tr class="item-row">
						  		<td>{{ $invoice->invoice_no }}</td>
						  		<td>{{ $invoice->pivot->payment_method }}</td>
						  		<td>{{ number_format( $invoice->pivot->total, 2, ".", ",") }}</td>
						  		<td>{{ $cashup->total > 0 ? number_format( $invoice->pivot->total / $cashup->total * 100, 2, ".", ",") : 0.00 }}</td>
						  		<td>{{ $invoice->pivot->payment_id ?: 'N/A' }}</td>
						  	</tr>
					 	@endif
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td><b>Total</b></td>
						<td><b>{{ $cashup->total > 0 ? number_format($cashup->total - $cashup->float_value, 2, ".", ",") : 0.00 }}</b></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table> 
			</div>
		</div>
	</div>
@endsection