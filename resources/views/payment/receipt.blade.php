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
	
	<title>Invoice Payment- {{ $payment->id }}</title>
	
</head>

<body data-gr-c-s-loaded="true">

	<div id="page-wrap">

		<div class="header-left">
			<strong class="font-header">{{ $payment->branch->owner }}</strong><br>
			Co Reg No: {{ $payment->branch->registration_no }}
			<br>
			<br>
			{{ $payment->branch->address }}<br>
			Phone: {{ $payment->branch->contact }}
			<br>
		</div>
	
		<div class="header-left header-center ">Invoice Payment</div>

		<div>
			<table cellpadding="3">
                <tbody>
				<tr>
                    <td class="meta-head">Served by</td>
                    <td><textarea>{{ auth()->user()->name }}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Payment Ref</td>
                    <td><textarea>{{$payment->id}}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Date</td>
                    <td><textarea>{{$payment->created_at->format('Y-m-d')}}</textarea></td>
                </tr>
                </tbody>
            </table>
		</div> 

		<br>

		<div class="outer-border">

			<div style="width: 100%; overflow: hidden;">
				<div style="width: 80px; float: left;"> 
					<strong class="text-left">Paid by:</strong>
				</div>
				<div style="margin-left: 80px;"> 
					<strong class="font-header">{{$payment->customer->name}}</strong>
					<br>
					<div class="text-left">{{$payment->customer->contact}}</div>
					<div class="text-left">{{$payment->customer->address1}}</div>
					<div class="text-left">{{$payment->customer->address2}}</div>
					<div class="text-left">{{$payment->customer->address3}}</div>
					<div class="text-left">{{$payment->customer->address4}}</div>
				</div>
			</div>
		</div>

		<div style="clear:both"></div>

		<div style="height: 200px">
			<table id="items" cellpadding="5" >
			<tbody>
				<tr>
					<th>Invoice Ref</th>
					<th>Invoice Date</th>
					<th>Payment RM</th>
					<th>Balance RM</th>
				</tr>
				

				@foreach($payment->payments as $list )
					<tr class="item-row">
						<td class="text-center">{{ $list->invoice->display_text}}</td>
						<td class="text-center">{{ $list->invoice->created_at->format('Y-m-d')}}</td>
						<td class="text-center">{{ number_format((float)$list->total,2,'.','')}}</td>
						<td class="text-center">{{ number_format((float)$list->outstanding,2,'.','')}}</td>
					</tr>

				@endforeach
				
				@for($x=0; $x < max( 26 - count($payment->payments), 0 ); $x++  )
					<tr class="item-row"><td style="height: 20px"></td></tr>
				@endfor

					<tr class="item-row">
						<th class="text-right" style="height: 20px; border: 1px solid black;" colspan="2">Transaction total</th>
						<td class="text-center" style="height: 20px; border: 1px solid black; font-weight: bold;">RM {{number_format((float)$payment->total,2,'.','')}}</td>
						<td style="border:1px solid black;"></td>
					</tr>
				</tbody>
			</table> 
		</div>
	<br>
		<div>
			<table class="header-left" cellpadding="4">
				<tr class="item-row">
					<th class="meta-head text-center" style="height: 20px" colspan='2'>Payment details</th>
				</tr>
				<tr class="item-row">
					<th class="meta-head text-center">Payment method</th>
					<td class="text-center" style="height: 20px; border: 1px solid black;">{{ $payment->payment_method}}</td>
				</tr>
				<tr class="item-row">
					<th class="meta-head text-center" style="height: 20px; border: 1px solid black;">Total paid</th>
					<td class="text-center" style="height: 20px; border: 2px solid black; font-weight: bold;">RM {{number_format((float)$payment->total,2,'.','')}}</td>
				</tr>
				</tbody>
			</table> 

		</div>
		<br>
		<div style="text-align: center; vertical-align: middle; line-height: 50px;height: 50px; font-size: 15px; font-weight: bold; float: left;">THANK YOU</div>

	</div>
</body></html>