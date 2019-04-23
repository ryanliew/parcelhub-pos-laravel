<table>
    <thead>
        <tr>
            <th>Vendor</th>
            <th>Amount (RM)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vendors as $name => $vendor_item)
        <tr>
            <td>{{ $name }}</td>
            <td>{{ number_format($vendor_item->sum('total_price'), 2, ".", "") }}</td>
        </tr>
        @endforeach
    </tbody>
</table>