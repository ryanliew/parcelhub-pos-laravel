<table>
    <thead>
        <tr>
            <th>Branch</th>
            <th>Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report_details as $detail)
            <tr>
                <td>{{ $detail['branch']->owner }}</td>
                <td>RM{{ number_format($detail['items']->sum('total_price_after_discount'), 2, ".", "") }}</td>
            </tr>
        @endforeach
    </tbody>
</table>