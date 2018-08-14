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

	    #items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
		#items th { background: #eee; }
		#items textarea { width: 80px; height: 50px; }
		#items tr.item-row td { border: 0; vertical-align: top; }

	</style>
	
	<title>Delivery order - {{ $invoice->display_text }}</title>
	
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
	
		<div class="header-left header-center ">Delivery Note</div>

		<div>
			<table cellpadding="3">
                <tbody>
                <tr>
                    <td class="meta-head">Type</td>
                    <td><textarea>Account sale</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Invoice #</td>
                    <td><textarea>{{$invoice->invoice_no}}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Invoice date</td>
                    <td><textarea>{{$invoice->created_at->format('Y-m-d')}}</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Printed date</td>
                    <td><textarea><?php echo date('Y-m-d');?></textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Served by</td>
                    <td><textarea>{{$invoice->user->name}}</textarea></td>
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

		@endif
		
		<div style="clear:both"></div>

		<div style="height: 200px">
			<table id="items" cellpadding="5" height=100% >
			  <tbody>
				  <tr>
				    <th>SKU code</th>
				    <th>Description</th>
			      	<th>Ordered</th>
			      	<th>Delivered</th>
				  </tr>
				  <?php $items = $invoice->items->groupBy("description")->toArray() ?>
				  @foreach($items as $key => $item)
				  	<tr class="item-row">
					  <td class="text-center">{{$item[0]['sku']}}</td>
					  <td class="text-left">{{$key}}</td>
					  <td class="text-center">{{collect($item)->count('id')}}</td>
					  <td class="text-center">{{collect($item)->count('id')}}</td>
					</tr>
					
					@foreach($item as $product)
						<tr class="item-row">
							<td class="text-center" colspan="2">S/No. {{ $product['tracking_code'] }} </td>
						</tr>
					@endforeach
					
				  @endforeach

				  @for($x=0; $x < max( 16 - count($invoice->items), 0 ); $x++  )
				  {
				  	<tr class="item-row"><td style="height: 20px"></td></tr>
				  }
				  @endfor

				</tbody>
			</table> 
		</div>

		<br>

		<div>
			<p class='bottom-left font-header'>Received by:</p>
			<p class='font-header'>Received on:</p>
		</div>

		<br><br>

		<div class='bottom-left-1'>
			<p class="bottom-left-1 border-top">name</p>
		</div>

		<div class="bottom-right">
			<p class="border-top">date</p>
		</div>

	</div>
	
	<br>	

	<textarea class='instructions'>Instructions: 
	{{$invoice->remarks}}</textarea>

</body></html>