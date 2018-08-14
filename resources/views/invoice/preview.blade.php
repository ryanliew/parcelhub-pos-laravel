<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	
		* { margin: 0; padding: 0; }
		body { font: 10px/1.4 Georgia, serif; }
		#page-wrap { width: 800px; margin: 0 auto; }

		textarea { border: 0; font: 14px Georgia, Serif; text-align: center; }
		table { border-collapse: collapse; }
		table td, table th { border: 1px solid black;  }

		.border-bottom {
	    	border-bottom: 2px solid #000;
	    }

	    .text-center {
	    	text-align: center; font-size: 14px;
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
	    	text-align: center; vertical-align: middle; line-height: 100px;height: 100px; font-size: 30px; font-weight: bold; float: left;
	    }

	    .font-header{
	    	font-size: 20px; font-weight: bold; 
	    }

	    .meta {
	    	 margin-top: 1px; width: 300px; float: right; 
	    }

	    .meta-head {
	    	text-align: left; background: #eee; width: 150px; font-size: 16px; font-weight: bold;
	    }

	    .meta-detail{
	    	text-align: left; background: #eee; width: 140px; font-size: 14px;
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

	    #items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
		#items th { background: #eee; }
		#items textarea { width: 80px; height: 50px; }
		#items tr.item-row td { border: 0; vertical-align: top; }

	</style>
	
	<title>Invoice - {{ $invoice->display_text }}</title>
	
</head>

<body data-gr-c-s-loaded="true">

	<div id="page-wrap">

		<div class="header-left">
			<strong class="font-header">{{ $invoice->branch->name }}</strong><br>
			Co Reg No: {{ $invoice->branch->registration_no }}
			<br>
			<br>
			{{ $invoice->branch->address }}<br>
			Phone: {{ $invoice->branch->contact }}
			<br>
		</div>
		<div><img id="image" src="img/logo.png" alt="logo"></div>
	
		<div class="header-left header-center ">Invoice</div>

		<div>
			<table cellpadding="3">
                <tbody>
                <tr>
                    <td class="meta-head">Invoice #</td>
                    <td><textarea>{{$invoice->display_text}}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Invoice date</td>
                    <td><textarea>{{$invoice->created_at->format('Y-m-d')}}</textarea></td>
                </tr>
                </tbody>
            </table>
		</div>
		
		@if($invoice->customer)

		<br>

		<div class="outer-border">
			<strong class="font-header">{{ $invoice->customer->name}}</strong>
			<br>
			<div class="text-left">{{ $invoice->customer->contact}}</div>
			<div class="text-left">{{$invoice->customer->address1}}</div>
			<div class="text-left">{{$invoice->customer->address2}}</div>
			<div class="text-left">{{$invoice->customer->address3}}</div>
			<div class="text-left">{{$invoice->customer->address4}}</div>

		</div>

		@else
			<div class="outer-border">
				<strong class="font-header">CASH</strong>
				<br>

			</div>
		@endif
		
		<div style="clear:both"></div>

		<div style="height: 200px">
			<table id="items" cellpadding="5" >
			  <tbody>
				  <tr>
				    <th>SKU code</th>
				    <th>Description</th>
			      	<th>Tax</th>
			      	<th>Price RM</th>
			      	<th>Qty</th>
			      	<th>Total RM</th>
				  </tr>
				<?php $items = $invoice->items->groupBy('description')->toArray() ?>

				@foreach($items as $key => $item)
					<tr class="item-row">
					  <td class="text-center">{{ $item[0]['sku'] }}</td>
					  <td class="text-left"  >{{ $key }} </td>
					  <td class="text-center">{{ number_format((float)$item[0]['tax'],2,'.','') }}</td>
					  <td class="text-center">{{ number_format((float)$item[0]['total_price'],2,'.','') }}</td>
					  <td class="text-center">{{ collect($item)->count('id') }}</td>
					  <td class="text-center">{{ number_format((float)collect($item)->sum('total_price'),2,'.','') }}</td>
					</tr>

					@foreach($item as $product)
						<tr class="item-row">
							<td class="text-center" colspan="2">S/No. {{ $product['tracking_code'] }} </td>
						</tr>
					@endforeach
					
				@endforeach
				
				@for($x=0; $x < max( 20 - count($invoice->items), 0 ); $x++  )
					<tr class="item-row"><td style="height: 20px"></td></tr>
				@endfor

				  <!-- @foreach($invoice->items as $item)
					<tr class="item-row">
					  <td class="text-center">{{$item->sku}}</td>
					  <td class="text-center">{{$item->description}}</td>
					  <td class="text-center">{{$item->tax}}</td>
					  <td class="text-center">{{number_format((float)$item->total_price,2,'.','')}}</td>
					  <td class="text-center">1</td>
					  <td class="text-center">{{number_format((float)$item->total_price,2,'.','')}}</td>
					</tr>
				  @endforeach
				  
				  @for($x=0; $x < max( 20 - count($invoice->items), 0 ); $x++  )
				  {
				  	<tr class="item-row"><td style="height: 20px"></td></tr>
				  }
				  @endfor -->

				</tbody>
			</table> 
		</div>

		<br><br><br>

		<div class="header-left">
			<table>
				<tbody>
				<tr class="item-row">
					<td class="meta-head text-center" colspan='2'>Invoice &amp; Attendant details</td>
				</tr>
				<tr class="item-row">
					<td class="meta-detail">Invoice #</td>
					<td><textarea>{{$invoice->display_text}}</textarea></td>
				</tr>
				<tr class="item-row">
					<td class="meta-detail">Payment due</td>
					<td><textarea>{{number_format((float)$invoice->total - $invoice->paid,2,'.','')}}</textarea></td>
				</tr>
				<tr class="item-row">
					<td class="meta-detail">Attendant</td>
					<td><textarea>{{$invoice->user->name}}</textarea></td>
				</tr>
				<tr class="item-row">
					<td style="text-align: left; width: 150px; height:80px; font-size: 14px;" rowspan '3' colspan='2'>
						<p>All cheque must be crossed &amp; made payable to:</p><br>
						<p>{{$invoice->branch->name}}</p>
						<p>{{$invoice->branch->payment_bank}} A/C No: {{$invoice->branch->payment_acc_no}}</p>
					
					</td>
				</tr>
				</tbody>
			</table>
		</div>

		<div >
			<table>
				<tbody>
				<tr class="item-row"><td class="meta-head text-center" colspan='2'>Invoice Totals (RM )</td></tr>
				<tr class="item-row"><td class="meta-detail">Subtotal</td><td ><textarea >{{number_format((float)$invoice->subtotal,2,'.','')}}</textarea></td></tr>
				<tr class="item-row"><td class="meta-detail">Discount</td><td><textarea>{{number_format((float)$invoice->discount,2,'.','')}}</textarea></td></tr>
				<tr class="item-row"><td class="meta-detail">Tax</td><td><textarea>{{number_format((float)$invoice->tax,2,'.','')}}</textarea></td></tr>
				<tr class="item-row"><td class="meta-detail">Rounding</td><td><textarea>{{number_format((float)$invoice->rounding,2,'.','')}}</textarea></td></tr>
				<tr class="item-row"><td class="meta-detail">Total</td><td><textarea>{{number_format((float)$invoice->total,2,'.','')}}</textarea></td></tr>
				</tbody>
			</table>
		</div>

	</div>
</body></html>