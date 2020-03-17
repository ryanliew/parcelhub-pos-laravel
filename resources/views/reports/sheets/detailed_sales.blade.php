<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Description</th>
            <th>Zone</th>
            <th>Vendor</th>
            <th>Weight</th>
            <th>Actual</th>
            <th>Type</th>
            <th>Invoice</th>
            <th>Tracking no.</th>
            <th>Amount (RM)</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->product->sku }}</td>
            <td>{{ $item->product->description }}</td>
            <td>{{ $item->product->zone }}</td>
            <td>@if($item->product->vendor_id){{ $item->product->vendor->name }} @else - @endif</td>
            <td>{{ $item->product->weight_start }} - {{ $item->product->weight_end }}</td>
            <td>{{ $item->weight }}</td>
            <td>{{ $item->product->product_type->name }}</td>
            <td>{{ $item->invoice->invoice_no }}</td>
            <td>{{ $item->tracking_code }}</td>
            <td>{{ number_format($item->total_price_after_discount, 2, ".", "") }}</td>
            <td>{{ $item->created_at->toDateString() }}</td>
        </tr>
        @endforeach
    </tbody>
</table>