<table id="items" cellpadding="5" >
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
            <tr>
                <td>{{ $item->posting_date }}</td>
                <td>{{ $item->pl_9 }}</td>
                <td>{{ $item->consignment_no }}</td>
                <td>{{ $item->weight }}</td>
                <td>{{ $item->zone }}</td>
                <td>{{ $item->subaccount }}</td>
                <td>{{ $item->charges }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
