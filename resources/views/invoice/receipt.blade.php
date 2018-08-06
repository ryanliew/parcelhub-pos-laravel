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
            font-size: 9px;
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

        .pl-2 {
        	padding-left: 2em;
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
		<strong>{{ $invoice->branch->name }}</strong><br>
		Co Reg No: {{ $invoice->branch->registration_no }}
		<br>
		<br>
		{{ $invoice->branch->address }}<br>
		Phone: {{ $invoice->branch->contact }}
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
					<td>{{ $invoice->branch->code }}{{ sprintf('%05d', $invoice->id) }}</td>
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
					<td>{{ $invoice->type }} @if($invoice->customer)- {{ $invoice->customer->name }}@endif</td>
				</tr>
			</tbody>
		</table>
		<br>
		<br>
		<table>
			<thead>
				<tr>
					<th class="text-left border-bottom">Description</th>
					<th class="text-left border-bottom">Price RM</th>
					<th class="text-right border-bottom" style="width: 20px;">Qty</th>
					<th class="text-right border-bottom">Total RM</th>
				</tr>
			</thead>
			<tbody>
				@foreach($invoice->items as $item)
					<tr>
						<td class="ptb-5">{{ $item->description }}</td>
						<td class="text-left ptb-5">{{ number_format($item->price, 2, '.', ',') }}</td>
						<td class="ptb-5 text-right">{{ $item->unit }}</td>
						<td class="text-right ptb-5">{{ number_format($item->total_price, 2, '.', ',') }}</td>
					</tr>
					<tr>
						<td colspan="4" class="pl-2 pb-5">S/No. {{ $item->tracking_code }}</td>
					</tr>

				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" class="border-top text-right"><b>Sub Total</b></td>
					<td class="border-top text-right">RM</td>
					<td class="text-right border-top">{{ number_format($invoice->subtotal, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="text-right" colspan="2"><b>Tax</b></td>
					<td class="text-right">RM</td>
					<td class="text-right">{{ number_format($invoice->tax, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="text-right" colspan="2"><b>Discount</b></td>
					<td class="text-right">RM</td>
					<td class="text-right">{{ number_format($invoice->discount, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="text-right" colspan="2"><b>Rounding</b></td>
					<td class="text-right">RM</td>
					<td class="text-right">{{ number_format($invoice->rounding, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="text-right" colspan="2"><b>Total</b></td>
					<td class="text-right">RM</td>
					<td class="text-right">{{ number_format($invoice->total, 2, '.', ',') }}</td>
				</tr>
			</tfoot>
		</table>

		<br>

		<strong>Payment Details - {{ $invoice->payment_type }}</strong>
		<table class="outer-border">
			<tbody>
				<tr>
					<td class="text-center" style="width: 215px;">Received:</td>
					<td>RM</td>
					<td class="text-right">{{ number_format($invoice->paid, 2, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="text-center">Change:</td>
					<td>RM</td>
					<td class="text-right">{{ number_format( max($invoice->paid - $invoice->total, 0.00), 2, '.', ',') }}</td>
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
