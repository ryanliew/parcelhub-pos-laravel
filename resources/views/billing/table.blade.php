<table id="items" cellpadding="2" >
    <thead>
        <tr>
            <th>Job Date</th>
            <th width="150">Job Type</th>
            <th>Consignment No.</th>
            <th>Weight</th>
            <th>Zone</th>
            <th width="80">Charges (RM)</th>
            <th width="80" style="border:0;">{{ isset($summary) ? "" : "Ref" }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($billing->items()->orderBy("subaccount")->orderBy("posting_date")->get() as $key => $item)
            @if(!isset($summary) && $key % 10 == 0)
                chunk
            @endif
            <tr class="item-row">
                <td>{{ $item->posting_date }}</td>
                <td width="150">{{ $item->pl_9 }}</td>
                <td>{{ $item->consignment_no }}</td>
                <td>{{ number_format((float)$item->weight,2,'.','') }}</td>
                <td>{{ $item->zone }}</td>
                <td style="text-align: right;" width="80">{{ number_format((float)$item->charges,2,'.','') }}</td>
                <td width="80">{{ isset($summary) ? substr($item->subaccount, 0, 10) : $item->subaccount }}</td>
            </tr>
        @endforeach

        @if(isset($summary))
            <tr><td colspan="6" style="height: 30px"></td></tr>
            <tr><td colspan="6" style="border-top: 1px solid black"></td></tr>
            <tr class="summary-row">
                <td colspan="5" style="text-align: right; margin-top: 50px;">Total</td>
                <td style="border-bottom: 1px solid black;"><b>{{ number_format((float)$billing->items->sum("charges"),2,'.','') }}</b></td>
                <td></td>
            </tr>
            <tr class="summary-row"><td style="height: 0px"></td></tr>
            <tr class="summary-row">
                <td colspan="5"></td>
                <td style="border-top: 1px solid black;"></td>
                <td></td>
            </tr>
            <tr class="summary-row"><td style="height: 15px"></td></tr>
            <tr class="summary-row" style="border-top: 1px solid black">
                <td style="text-align: left;"><b>Total Item</b></td>
                <td style="text-align: left;">{{ $billing->items->count() }}</td>
                <td colspan="3" style="text-align: right">Courier Charges - Domestic</td>
                <td>{{ number_format((float)$billing->items->sum("charges"),2,'.','') }}</td>
                <td></td>
            </tr>
            <tr class="summary-row" style="border-top: 1px solid black">
                <td colspan="5" style="text-align: right">GST 0% (SR)</td>
                <td>0.00</td>
                <td></td>
            </tr>
            <tr class="summary-row" style="border-top: 1px solid black">
                <td colspan="5" style="text-align: right">Grand Total</td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black;"><b>{{ number_format((float)$billing->items->sum("charges"),2,'.','') }}</b></td>
                <td></td>
            </tr>
            <tr class="summary-row"><td style="height: 0px"></td></tr>
            <tr class="summary-row">
                <td colspan="5"></td>
                <td style="border-top: 1px solid black;"></td>
                <td></td>
            </tr>
{{--            <tr class="summary-row"><td style="height: 15px"></td></tr>--}}
{{--            <tr class="summary-row">--}}
{{--                <td colspan="5" style="text-align: right">Courier Charges - International</td>--}}
{{--                <td>0.00</td>--}}
{{--                <td></td>--}}
{{--            </tr>--}}
{{--            <tr class="summary-row">--}}
{{--                <td colspan="5" style="text-align: right">GST 0% (SR)</td>--}}
{{--                <td>0.00</td>--}}
{{--                <td></td>--}}
{{--            </tr>--}}
{{--            <tr class="summary-row">--}}
{{--                <td colspan="5" style="text-align: right">Subtotal</td>--}}
{{--                <td style="border-top: 1px solid black; border-bottom: 1px solid black;"><b>0.00</b></td>--}}
{{--                <td></td>--}}
{{--            </tr>--}}
{{--            <tr class="summary-row"><td style="height: 0px"></td></tr>--}}
{{--            <tr class="summary-row">--}}
{{--                <td colspan="5"></td>--}}
{{--                <td style="border-top: 1px solid black;"></td>--}}
{{--                <td></td>--}}
{{--            </tr>--}}
{{--            <tr class="summary-row"><td style="height: 15px"></td></tr>--}}
{{--            <tr class="summary-row">--}}
{{--                <td colspan="5" style="text-align: right">Grand Total</td>--}}
{{--                <td style="border-top: 1px solid black; border-bottom: 1px solid black"><b>{{ number_format((float)$billing->items->sum("charges"),2,'.','') }}</b></td>--}}
{{--                <td></td>--}}
{{--            </tr>--}}
{{--            <tr class="summary-row"><td style="height: 0px"></td></tr>--}}
{{--            <tr class="summary-row">--}}
{{--                <td colspan="5"></td>--}}
{{--                <td style="border-top: 1px solid black;"></td>--}}
{{--                <td></td>--}}
{{--            </tr>--}}
        @endif
    </tbody>
</table>
