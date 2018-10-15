<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	
		* { margin: 0; padding: 0; }
		body { font: 10px/1.4 Georgia, serif; }
		#page-wrap { width: 800px; margin: 0 auto; }

		textarea { border: 0; font: 14px Georgia, Serif; }
		table { border-collapse: collapse; }
		table td, table th { border: 1px solid black;  }

		.border-bottom {
	    	border-bottom: 2px solid #000;
	    }

	    .text-center {
	    	text-align: center;
	    }

	    .text-left {
	    	text-align: left; font-size: 14px;
	    }

	    .text-right {
	    	text-align: right;
	    }

	    .header-left {
	    	width: 400px; height: 80px; float: left;
	    }

	    .header-center {
	    	text-align: center; vertical-align: middle; line-height: 200px;height: 200px; font-size: 30px; font-weight: bold; float: left;
	    }

	    .font-header{
	    	font-size: 20px; font-weight: bold; 
	    }

	    .meta {
	    	 margin-top: 1px; width: 300px; float: right; 
	    }

	    .meta-head {
	    	text-align: left; background: #eee; width: 150px; font-size: 16px;
	    }

	    .header-note {
	    	width: 250px; height: 80px; float: left; font-size: 30px; font-weight: bold; text-align: center; vertical-align: middle;
	    }

	    .instructions {
	    	width: 750px ; height: 120px; float: left; border: solid black 1px; border-radius: 15px; padding: 20px;
	    }

	    .border-top {
	    	border-top: 2px solid #000;
	    }

	    .outer-border {
	    	border: 1px solid #ccc; border-radius: 15px; padding: 20px;
	    }

	    .bottom-left{
	    	width: 400px; height: 1px; float: left;
	    }

	    .bottom-center{
	    	width: 1000px; height: 10px; float: right;
	    }

	    .bottom-left-1{
	    	width: 300px; height: 50px; float: left;
	    }
	    .bottom-right{
	    	width: 300px; float: right;
	    }

	    .page-break {
	    	page-break-before: always;
	    }

	    #items, #invoices { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
		#items th, #invoices th { background: #eee; }
		#items textarea, #invoices textarea { width: 80px; height: 50px; }
		#items tr.item-row td, #invoices tr.item-row td { border: 0; vertical-align: top; }

	</style>
	
	<title>Cashup report</title>
	
</head>

<body data-gr-c-s-loaded="true">

	<div id="page-wrap">

		<div class="header-left">
			<strong class="font-header">{{ $cashup->branch->owner }}</strong><br>
			Co Reg No: {{ $cashup->branch->registration_no }}
			<br>
			<br>
			{{ $cashup->branch->address }}<br>
			Phone: {{ $cashup->branch->contact }}
			<br>
		</div>
	
		<div class="header-left header-center ">Cash up report</div>

		<div>
			<table cellpadding="3">
                <tbody>
                <tr>
                    <td class="meta-head">Drawer</td>
                    <td><textarea>{{ $cashup->terminal->name }}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Transactions</td>
                    <td><textarea>{{ $cashup->invoice_from }} - {{ $cashup->invoice_to }}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Session opened</td>
                    <td><textarea>{{ $cashup->session_start->toDayDateTimeString() }}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Report run</td>
                    <td><textarea>{{ $cashup->created_at->toDayDateTimeString() }}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Created by</td>
                    <td><textarea>{{ $cashup->creator->name }}</textarea></td>
                </tr>
                </tbody>
            </table>
		</div>
		
		<div style="clear:both"></div>

		<div style="height: 200px">
			<table id="items" cellpadding="5" height=100% >
			  	<tbody>
				  	<tr>
				   		<th>Legend</th>
					    <th>Payment Type</th>
				      	<th>Expected RM</th>
				      	<th>%</th>
				      	<th>Count</th>
				  	</tr>
				  	@foreach($invoices->groupBy(function($item){ return $item->pivot->payment_method; }) as $type => $records)
					  	<?php $sum = $records->sum(function($invoice){ return $invoice->pivot->total; }); ?>
				  		@if($sum > 0)
						  	<tr class="item-row text-center">
						  		<td class="text-center">
						  			@if($type == 'IBG')17
						  			@elseif($type == 'Cash')01
						  			@elseif($type == 'Credit card')02
						  			@elseif($type == 'Cheque')05
						  			@endif
						  		</td>
						  		<td class="text-center">{{ $type }}</td>
						  		<td class="text-center">{{ number_format( $sum, 2, ".", ",") }}</td>
						  		<td class="text-center">{{ $cashup->total > 0 ? number_format( $sum / $cashup->total * 100, 2, ".", ",") : 0.00 }}</td>
						  		<td class="text-center">{{ $records->count() }}</td>
						  	</tr>
					  	@endif
					@endforeach

				  	<tr class="item-row text-center">
				  		<td class="text-center">00</td>
				  		<td class="text-center">Float</td>
				  		<td class="text-center">{{ number_format( $cashup->float_value, 2, ".", ",") }}</td>
				  		<td class="text-center">{{ $cashup->total > 0 ? number_format($cashup->float_value / $cashup->total * 100, 2, ".", ",") : 0.00}}</td>
				  		<td></td>
				  		<td></td>
				  	</tr>

				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td class="text-right"><b>Total</b></td>
						<td class="text-center"><b>{{ number_format($cashup->total, 2, ".", ",") }}</b></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table> 
		</div>

		<div class="page-break">
			<h1>Including invoices</h1>
			<table id="invoices" cellpadding="5" height=100% >
			  	<tbody>
				  	<tr>
				   		<th>Invoice no</th>
					    <th>Payment Type</th>
				      	<th>Expected RM</th>
				      	<th>%</th>
				      	<th>Payment #</th>
				  	</tr>
				  	@foreach($invoices as $invoice)
				  		@if($invoice->pivot->total !== 0)
						  	<tr class="item-row text-center">
						  		<td class="text-center">{{ $invoice->invoice_no }}</td>
						  		<td class="text-center">{{ $invoice->pivot->payment_method }}</td>
						  		<td class="text-center">{{ number_format( $invoice->pivot->total, 2, ".", ",") }}</td>
						  		<td class="text-center">{{ $cashup->total > 0 ? number_format( $invoice->pivot->total / $cashup->total * 100, 2, ".", ",") : 0.00 }}</td>
						  		<td class="text-center">{{ $invoice->pivot->payment_id ?: 'N/A' }}</td>
						  	</tr>
					 	@endif
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td class="text-right"><b>Total</b></td>
						<td class="text-center"><b>{{ $cashup->total > 0 ? number_format($cashup->total - $cashup->float_value, 2, ".", ",") : 0.00 }}</b></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table> 
		</div>
</body>
</html>