<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
    	@page { margin-top: 30px; margin-bottom: 0px; margin-left: 15px; margin-right: 15px; }
    	body, html {
            width: 100%;
            height: auto;
            padding: 0;
            margin: 1px;
            font-family: 'monaco';
            font-size: 11px;
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

        table {
        	width: 100%;
        	border-collapse: collapse;
        }

        tbody {
        	padding: 10px 0;
        }

        .pl-1 {
        	padding-left: 1em;
        }

        .pl-2 {
        	padding-left: 2em;
        }

        .pl-5 {
        	padding-left: 5em;
        }

        .pr-1 {
        	padding-right: 1em;
        }

        .ptb-5 {
        	padding-top: 5px;
        	padding-bottom: 5px;
        }

        .pb-5 {
        	padding-bottom: 5px;
        }

        .border-bottom {
        	border-bottom: 2px solid #000;
        }

        .border-top {
        	border-top: 2px solid #000;
        }

        .outer-border {
        	border: 1px solid #ccc;
        }
    </style>
    <title>
    	Receipt for {{ $session->table->name }}
    </title>
</head>
<body>
	<div class="text-center">
		<strong>{{ $session->table->branch->owner }}</strong><br>
		Co Reg No: {{ $session->table->branch->registration_no }}
		<br>
		<br>
		{{ $session->table->branch->address }}<br>
		Phone: {{ $session->table->branch->contact }}
		<br>
		<br>
		<br>
		Receipt<br><br>
	</div>

	<div class="content">
		<table>
			<tbody>
				<tr>
					<td>Receipt no</td>
					<td>:</td>
					<td>R-{{ sprintf("%08d", $session->id) }}</td>
				</tr>
				<tr>
					<td>Served by</td>
					<td>:</td>
					<td>{{ $session->invoices()->latest()->first()->user->name }}</td>
				</tr>
				<tr>
					<td>Date</td>
					<td>:</td>
					<td>{{ $session->updated_at->toDateTimeString() }}</td>
				</tr>
				<tr>
					<td>Sold to</td>
					<td>:</td>
					<td>Cash</td>
				</tr>
			</tbody>
		</table>
		<br>
		<br>
		<table>
			<thead>
				<tr>
					<th class="text-left border-bottom">Description SKU</th>
					<th class="text-right border-bottom">Price RM</th>
					<th class="text-center border-bottom" style="width: 30px;">Qty</th>
					<th class="text-right border-bottom">Total RM</th>
				</tr>
			</thead>
			<tbody>
				<?php 

					$grouped_items = $items
									->groupBy([
										'description',
										function($item){
											return (int)($item->price * 100);
										},
									]); 
				?>
				@foreach($grouped_items->toArray() as $dkey => $description)
					@foreach($description as $key => $item)
						<tr>
							<td class="ptb-5" colspan="4">{{ $dkey }}</td>
						</tr>
						<tr>
							<td class="text-right ptb-5" colspan="2">{{ number_format($item[0]['price'], 2, '.', ',') }}</td>
							<td class="ptb-5 text-right pr-1">{{ collect($item)->sum('unit') }}</td>
							<td class="text-right ptb-5">{{ number_format(collect($item)->sum('total_price'), 2, '.', ',') }}</td>

						</tr>
					@endforeach

				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" class="border-top pl-5 text-left"><b>Sub Total</b></td>
					<td class="border-top text-center">RM</td>
					<td class="text-right border-top">{{ number_format($items->sum('price'), 2, '.', ',') }}</td>
				</tr>
				{{-- <tr>
					<td class="pl-5 text-left" colspan="2"><b>Tax</b></td>
					<td class="text-center">RM</td>
					<td class="text-right">{{ number_format($items->sum('tax'), 2, '.', ',') }}</td>
				</tr> --}}
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Discount</b></td>
					<td class="text-center">RM</td>
					<td class="text-right">{{ number_format($session->discount_value, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Rounding</b></td>
					<td class="text-center">RM</td>
					<td class="text-right">{{ number_format($session->rounding, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Total</b></td>
					<td class="text-center">RM</td>
					<td class="text-right">{{ number_format($session->total, 2, '.', ',') }}</td>
				</tr>
			</tfoot>
		</table>

		<br>

		<strong>Payment Details - {{ $session->payment_type }}</strong>
		<table class="outer-border">
			<tbody>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Received:</b></td>
					<td>RM</td>
					<td class="text-right">{{ number_format($session->paid, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Change:</b></td>
					<td>RM</td>
					<td class="text-right">{{ number_format( max($session->paid - $session->total, 0.00), 2, '.', ',') }}</td>
				</tr>
			</tbody>
		</table>
		<br>
		
		<br>
		<br>
		<div class="text-center">
			Thank you
		</div>
	</div>
</body>
