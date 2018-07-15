<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	
		* { margin: 0; padding: 0; }
		body { font: 14px/1.4 Georgia, serif; }
		#page-wrap { width: 800px; margin: 0 auto; }

		textarea { border: 0; font: 16px Georgia, Serif; overflow: hidden; resize: none; }
		table { border-collapse: collapse; }
		table td, table th { border: 1px solid black; padding: 5px;}

		.border-bottom {
	    	border-bottom: 2px solid #000;
	    }

	    .text-center {
	    	text-align: center;
	    }

	    .text-left {
	    	text-align: left;
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
	    	 margin-top: 1px; width: 380px; float: right;
	    }

	    .meta-head {
	    	text-align: left; background: #eee; width: 200px; font-size: 18px;
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
	    	width: 300px; height: 100px; float: left;
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
		
		<div style="clear:both"></div>

		<div class="header-left header-center ">Delivery Note</div>
		<div>
			<table class="meta">
                <tbody>
                <tr>
                    <td class="meta-head">Type</td>
                    <td><textarea>Account sale</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Invoice #</td>
                    <td><textarea>{{$invoice->display_text}}</textarea></td>
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
			<br><br>
			{{ $invoice->customer->contact}}
			<br>
			{{$invoice->customer->address1}}
			<br>
			{{$invoice->customer->address2}}
			<br>
			{{$invoice->customer->address3}}
			<br>
			{{$invoice->customer->address4}}

		</div>

		@endif
		
		<div style="clear:both"></div>

		<div style="height: 200px">
			<table id="items" >
			  <tbody>
				  <tr>
				    <th>SKU code</th>
				    <th>Description</th>
			      	<th>Ordered</th>
			      	<th>Delivered</th>
				  </tr>

				  @foreach($invoice->items as $item)
					<tr class="item-row">
					  <td class="text-center">{{$item->sku}}</td>
					  <td class="text-center">{{$item->description}}</td>
					  <td class="text-center">1</td>
					  <td class="text-center">1</td>
					</tr>
				  @endforeach

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
			<p class="border-top">signature</p>
		</div>

		<div class="bottom-right">
			<p class="border-top">date</p>
		</div>

	</div>
	
	<br>	

	<textarea class='instructions'>Instructions: 
	{{$invoice->remarks}}</textarea>

</body></html>