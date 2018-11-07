<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	
		* { margin: 0; padding: 0; }
		body { font: 11px/1.4 Georgia, serif; }
		#page-wrap { width: 800px; margin: 0 auto; }

		textarea { border: 0; font: 14px Georgia, Serif; }
		table { border-collapse: collapse;  }

		.border-bottom {
	    	border-bottom: 2px solid #000;
	    }

	    .text-center {
	    	text-align: center; font-size: 14px;
	    }

	    .text-left {
	    	text-align: left; font-size: 14px;
	    }

	    .text-left-bold {
	    	text-align: left; font-size: 14px; font-weight: bold;
	    }

	    .text-right {
	    	text-align: right;
	    }

	    .header-left {
	    	width: 400px; height: 40px; float: left;
	    }

	    .header-center {
	    	vertical-align: middle; text-align: center; font-weight: bold; font-size: 16px
			/* width: 400px; text-align: center; vertical-align: middle; line-height: 12px; height: 10px; font-size: 18px; font-weight: bold; float: left; */
	    }

	    .font-header{
	    	font-size: 20px; font-weight: bold; 
	    }

	    .font-detail
	    {
	    	font-size: 16px;
	    }

	    .meta {
	    	 margin-top: 1px; width: 500px; float: right; 
	    }

	    .meta-head {
	    	text-align: left; background: #eee; width: 150px; font-size: 15px;
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

	    #items { clear: both; width: 100%; margin: 0px 0 0 0;  } 
		#items th { background: #eee; }
		#items textarea { width: 80px; height: 50px; }
		#items tr.item-row td { border: 0; vertical-align: top; border: 1px solid black; }

		#items1 { clear: both; width: 100%; margin: 0px 0 0 0;  } 
		#items1 th { background: #eee; }
		#items1 textarea { width: 80px; height: 50px; }
		#items1 tr.item-row td { border: 0; vertical-align: top; }

		.soa-td{
			text-align: left; font-size: 18px; font: bold; width: 175px; padding-left: 10px
		}
	</style>
	
	<title>Statement Of Account</title>
	
</head>

<body data-gr-c-s-loaded="true">

	<div id="page-wrap">
		<div class="border-bottom" style="padding-bottom: 50px">
			<div class="header-center">
				<strong>{{ $customer->branch->owner }}({{ $customer->branch->registration_no }}) </strong> 
				@if($customer->branch->gst_no )
					<strong>| (GST No: {{$customer->branch->gst_no }})</strong>
				@endif

			</div>
			<div style="vertical-align: middle; text-align: center;"> 
				{{ $customer->branch->address }}
			</div>
			<div style="vertical-align: middle; text-align: center;"> 
				Tel: {{ $customer->branch->contact }} | Fax: {{$customer->branch->fax}}
			</div>

			<!-- <div>
				<img id="image" src="img/logo.png" alt="logo">
			</div> -->
		</div>
	Customer
		<br>
	<div class="border-bottom" >
		<div class="header-left" >
			<br>
			<div class="text-left font-detail"><strong>{{ $customer->name}}</strong></div>
			<div class="text-left">{{$customer->address1}}, {{$customer->address2}}, {{$customer->address3}},{{$customer->address4}} </div>
			<br>
			
			<div class="text-left" style="float: left; width: 10%">Tel</div><div class="text-left">{{$customer->contact}}</div>
			<div class="text-left" style="float: left; width: 10%">Fax</div><div class="text-left">{{$customer->fax}}</div>

		</div>

		<div>

			<table style="border: 1px solid black;">
				<tr>
					<td colspan='3' style="border-bottom: 1px solid black;text-align: center; font-size: 22px; font: bold;"><strong>Statement of account</strong></td>
				</tr>
				<tr >
					<td class="soa-td" style="padding-top: 10px;">Total Debit ( {{$debit_count}} )</td>
					<td class="soa-td" style="padding-top: 10px; padding-right: 10px;text-align: right">{{number_format((float)$debit,2,'.','')}}</td>
				</tr>

				<tr>
					<td class="soa-td" style="border-bottom: 3px solid black">Total Credit ( {{$credit_count}} )</td>
					<td class="soa-td" style="border-bottom: 3px solid black;padding-right: 10px; text-align: right">{{number_format((float)$credit,2,'.','')}}</td>
				</tr>

				<tr>
					<td class="soa-td" style="padding: 10px">Closing Balance</td>
					<td class="soa-td" style="padding: 10px; text-align: right">{{number_format((float)$resultBalance,2,'.','')}}</td>
				</tr>
				
			</table>


			
		</div>
	</div>
		
	<div class="border-bottom" style="padding-top: 5px">
		<table id="items1" style="border: 0">
		  <tbody>
			<tr class="item-row">
				<td>Customer Account</td>
				<td>Attendant</td>
				<td>Currency</td>
				<td>Terms</td>
				<td>Date</td>
			</tr>
			<tr class="item-row">
				<td class="text-left-bold">{{ $customer->branch->code}}{{ $customer->branch->id}}_{{120 + number_format((float)$customer->id,0,'.','')}}</td>
				<td class="text-left-bold">{{$attendant}}</td>
				<td class="text-left-bold">RM</td>
				<td class="text-left-bold">30 days</td>
				<td class="text-left-bold"><?php echo date('Y-m-d');?></td>
			</tr>

			</tbody>
		</table> 
	</div>

	<div class="border-top border-bottom" style="height: 200px">
		<br>
		<table id="items1" cellpadding="5" height=100% >
		  <tbody>
			  <tr class="item-row" >
			    <th>Date</th>
			    <th>Reference</th>
			    <th style="width: 150px;">Transaction Description</th>
		      	<th> Debit </th>
		      	<th> Credit </th>
		      	<th>Balance</th>
			  </tr>

			@foreach($result as $key => $collection)
				<tr class="item-row">
				  	<td class="text-center">{{ $collection['date']->format('Y-m-d') }}</td>
					<td class="text-center">{{ $collection['ref'] }}</td>
					<td class="text-center">{{ $collection['desc'] }}</td>

				@if( $collection['debit'] == 1 )
					<td class="text-center">{{number_format((float)$collection['total'],2,'.','')}}</td>
				@else
					<td class="text-center"></td>
				@endif

				@if( $collection['debit'] != 1 )
					<td class="text-center">{{number_format((float)$collection['total'],2,'.','')}}</td>
				@elseif( array_key_exists('paid', $collection) )
					<td class="text-center">{{number_format((float)$collection['paid'],2,'.','')}}</td>
				@else
					<td class="text-center"></td>
				@endif

					<td class="text-center" >{{number_format((float)round($collection['balance'], 2),2,'.','')}}</td>
				</tr>
				@endforeach

			@for($x=0; $x < max( 35 - count($result), 0 ); $x++  )
			  {
			  	<tr class="item-row"><td style="height: 20px"></td></tr>
			  }
			  @endfor
			</tbody>
		</table> 
	</div>

	<div class="border-top">
		<div style="width: 600px; float: left;">
			<strong style="font-size: 14px; ">RINGGIT MALAYSIA : {{$balance_en}}</strong> 
		</div>
		<div >
			<strong style="font-size: 14px">RM  {{number_format((float)$resultBalance,2,'.','')}}</strong>
		</div>
		<br>
	</div>

	<div>
		<div style="height: 50px">
		<br>
		<table id="items" cellpadding="2" height=100% style="border: 1px solid black; " >
		  <tbody>
			<tr class="item-row" >
				<th>Current Mth</th>
				<th>1 Month</th>
				<th>2 Month</th>
				<th>3 Month</th>
				<th>4 Month</th>
				<th>5 Month++</th>
			</tr>
			<tr class="item-row">
				<td class="text-center">{{number_format((float)$outstanding['current'],2,'.','')}}</td>
				<td class="text-center">{{number_format((float)$outstanding['1'],2,'.','')}}</td>
				<td class="text-center">{{number_format((float)$outstanding['2'],2,'.','')}}</td>
				<td class="text-center">{{number_format((float)$outstanding['3'],2,'.','')}}</td>
				<td class="text-center">{{number_format((float)$outstanding['4'],2,'.','')}}</td>
				<td class="text-center">{{number_format((float)$outstanding['5'],2,'.','')}}</td>
			</tr>
			</tbody>
		</table> 
We shall be grateful if you will let us have payment as soon as possible. Any discrepancy in this statement must be reported to us in writing within 10 days.
	</div>


	</div>


	</div>

</body></html>