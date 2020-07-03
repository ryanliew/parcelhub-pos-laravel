<table>
    <thead>
        <tr>
            <th>SKU type</th>
            <th>Amount (RM)</th>
            <th>Branch</th>
        </tr>
    </thead>
    <tbody>
        @foreach($all_branches_skutypes as $detail)
            @foreach($detail['skutypes'] as $name => $skutype_item)
            <tr>
                <td>{{ $name }}</td>
                <td>{{ number_format($skutype_item->sum('total_price_after_discount'), 2, ".", "") }}</td>
                <td>{{ $detail['branch']->name}}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>