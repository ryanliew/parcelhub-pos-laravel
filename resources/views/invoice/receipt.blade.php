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
    	Receipt for {{ $invoice->invoice_no }}
    </title>
</head>
<body>
	<div class="text-center">
		<strong>{{ $invoice->branch->owner }}</strong><br>
		Co Reg No: {{ $invoice->branch->registration_no }}
		<br>
		<br>
		{{ $invoice->branch->address }}<br>
		Phone: {{ $invoice->branch->contact }}
		<br>
		<br>
		<br>
		{{ $invoice->receipt_type }}<br><br>
	</div>

	<div class="content">
		<table>
			<tbody>
				<tr>
					<td>{{ $invoice->receipt_type }} no</td>
					<td>:</td>
					<td>{{ $invoice->invoice_no }}</td>
				</tr>
				<tr>
					<td>Served by</td>
					<td>:</td>
					<td>{{ $invoice->user->name }}</td>
				</tr>
				<tr>
					<td>Date</td>
					<td>:</td>
					<td>{{ $invoice->updated_at->toDateTimeString() }}</td>
				</tr>
				<tr>
					<td>Sold to</td>
					<td>:</td>
					<td>@if($invoice->customer){{ $invoice->customer->name }} @else {{ $invoice->type }}  @endif</td>
				</tr>
				@if($invoice->customer)
				<tr>
					<td></td>
					<td></td>
					<td>
						{{ $invoice->customer->address1 }}<br>
						@if($invoice->customer->address2) {{ $invoice->customer->address2 }}<br> @endif
						@if($invoice->customer->address3) {{ $invoice->customer->address3 }}<br> @endif
						@if($invoice->customer->address4) {{ $invoice->customer->address4 }}<br> @endif

						@if($invoice->customer->contact) PH: {{ $invoice->customer->contact }} @endif

					</td>
				</tr>
				@endif
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
					$sessions = $invoice->sessions()->with('items')->get();
					$items = $sessions->pluck('items')
								->flatten()
								->groupBy([
									'description',
									function($item){
										return (int)($item->price * 100);
									},
								]); 
				?>
				@foreach($items->toArray() as $dkey => $description)
					@foreach($description as $key => $item)
						<tr>
							<td class="ptb-5" colspan="4">{{ $dkey }}</td>
						</tr>
						<tr>
							<td class="text-right ptb-5" colspan="2">
								@if($item[0]['member_id'])
									{{ number_format($item[0]['member_price'], 2, '.', ',')}}
								@else
									{{ number_format($item[0]['price'], 2, '.', ',') }}
								@endif
							</td>
							<td class="ptb-5 text-right pr-1">{{ collect($item)->sum('unit') }}</td>
							<td class="text-right ptb-5">

								@if($item[0]['member_id'])
									{{ number_format(collect($item)->sum(function($item){ $item['member_price'] * $item['unit']; }), 2, '.', ',') }}
								@else
									{{ number_format(collect($item)->sum('total_price'), 2, '.', ',') }}
								@endif
							</td>
						</tr>
					@endforeach

				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" class="border-top pl-5 text-left"><b>Sub Total</b></td>
					<td class="border-top text-center">RM</td>
					<td class="text-right border-top">{{ number_format($invoice->subtotal, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Tax</b></td>
					<td class="text-center">RM</td>
					<td class="text-right">{{ number_format($invoice->tax, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Discount</b></td>
					<td class="text-center">RM</td>
					<td class="text-right">{{ number_format($invoice->discount_value, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Rounding</b></td>
					<td class="text-center">RM</td>
					<td class="text-right">{{ number_format($invoice->rounding, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Total</b></td>
					<td class="text-center">RM</td>
					<td class="text-right">{{ number_format($invoice->total, 2, '.', ',') }}</td>
				</tr>
			</tfoot>
		</table>

		<br>

		<strong>Payment Details - {{ $invoice->payment_type }}</strong>
		<table class="outer-border">
			<tbody>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Received:</b></td>
					<td>RM</td>
					<td class="text-right">{{ number_format($invoice->paid, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="pl-5 text-left" colspan="2"><b>Change:</b></td>
					<td>RM</td>
					<td class="text-right">{{ number_format( max($invoice->paid - $invoice->total, 0.00), 2, '.', ',') }}</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table>
			<tr>
				<td>Tax Summary:</td>
				<td>Amount (RM)</td>
				<td>Tax (RM)</td>
			</tr>
			@foreach($taxes as $tax)
			<tr>
				<td>{{ $tax->code }} @ {{ $tax->percentage }}%</td>
				<?php $filtered = $sessions->pluck('items')->flatten()->filter(function($item) use ($tax){ return $item->tax_type == $tax->code; }) ?>
				<td>{{ number_format( $filtered->sum('price'), 2, '.', ',') }}</td>
				<td>{{ number_format( $filtered->sum('tax'), 2, '.', ',') }}</td>
			</tr>
			@endforeach
		</table>
		<br>
		<br>
		<br>
		<div class="text-center">
			Thank you
		</div>
	</div>
</body>
