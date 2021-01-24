<table id="items" cellpadding="2" >
    <thead>
        <tr>
            <th>Posting Date</th>
            <th>PL 9.</th>
            <th>Consignment No.</th>
            <th>Weight</th>
            <th>Zone</th>
            <th>Ref</th>
            <th>Charges (RM)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($billing->items as $item)
            <tr class="item-row">
                <td>{{ $item->posting_date }}</td>
                <td>{{ $item->pl_9 }}</td>
                <td>{{ $item->consignment_no }}</td>
                <td>{{ $item->weight }}</td>
                <td>{{ $item->zone }}</td>
                <td>{{ $item->subaccount }}</td>
                <td style="text-align: right;">{{ $item->charges }}</td>
            </tr>
        @endforeach

        @if(isset($summary))
            <tr><td colspan="7" style="height: 30px"></td></tr>
            <tr><td colspan="7" style="border-top: 1px solid black"></td></tr>
            <tr class="summary-row">
                <td colspan="6" style="text-align: right; margin-top: 50px;">Total</td>
                <td style="border-bottom: 1px solid black;"><b>{{ number_format((float)$billing->items->sum("charges"),2,'.','') }}</b></td>
            </tr>
            <tr class="summary-row"><td style="height: 0px"></td></tr>
            <tr class="summary-row">
                <td colspan="6"></td>
                <td style="border-top: 1px solid black;"></td>
            </tr>
            <tr class="summary-row"><td style="height: 15px"></td></tr>
            <tr class="summary-row" style="border-top: 1px solid black">
                <td style="text-align: left;">Total Item</td>
                <td style="text-align: left;">{{ $billing->items->count() }}</td>
                <td colspan="4" style="text-align: right">Courier Charges - Domestic</td>
                <td>{{ number_format((float)$billing->items->sum("charges"),2,'.','') }}</td>
            </tr>
            <tr class="summary-row" style="border-top: 1px solid black">
                <td colspan="6" style="text-align: right">GST 0% (SR)</td>
                <td>0.00</td>
            </tr>
            <tr class="summary-row" style="border-top: 1px solid black">
                <td colspan="6" style="text-align: right">Subtotal</td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black;"><b>{{ number_format((float)$billing->items->sum("charges"),2,'.','') }}</b></td>
            </tr>
            <tr class="summary-row"><td style="height: 0px"></td></tr>
            <tr class="summary-row">
                <td colspan="6"></td>
                <td style="border-top: 1px solid black;"></td>
            </tr>
            <tr class="summary-row"><td style="height: 15px"></td></tr>
            <tr class="summary-row">
                <td colspan="6" style="text-align: right">Courier Charges - International</td>
                <td>0.00</td>
            </tr>
            <tr class="summary-row">
                <td colspan="6" style="text-align: right">GST 0% (SR)</td>
                <td>0.00</td>
            </tr>
            <tr class="summary-row">
                <td colspan="6" style="text-align: right">Subtotal</td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black;"><b>0.00</b></td>
            </tr>
            <tr class="summary-row"><td style="height: 0px"></td></tr>
            <tr class="summary-row">
                <td colspan="6"></td>
                <td style="border-top: 1px solid black;"></td>
            </tr>
            <tr class="summary-row"><td style="height: 15px"></td></tr>
            <tr class="summary-row">
                <td colspan="6" style="text-align: right">Grand Total</td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black"><b>{{ number_format((float)$billing->items->sum("charges"),2,'.','') }}</b></td>
            </tr>
            <tr class="summary-row"><td style="height: 0px"></td></tr>
            <tr class="summary-row">
                <td colspan="6"></td>
                <td style="border-top: 1px solid black;"></td>
            </tr>
        @endif
    </tbody>
</table>
