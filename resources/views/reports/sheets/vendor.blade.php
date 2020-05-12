<table>
    <thead>
        <tr>
            <th>Vendor</th>
            <th>Amount (RM)</th>
            <th>Branch</th>
        </tr>
    </thead>
    <tbody>
        @foreach($all_branches_vendors as $detail)
            @foreach($detail['vendors'] as $name => $vendor_item)
            <tr>
                <td>{{ $name }}</td>
                <td>{{ number_format($vendor_item->sum('total_price_after_discount'), 2, ".", "") }}</td>
                <td>{{ $detail['branch']->name}}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>