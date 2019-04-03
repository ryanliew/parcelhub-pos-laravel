<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Description</th>
            <th>Zone</th>
            <th>Vendor</th>
            <th>Weight</th>
            <th>Type</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $sku => $product)
        <tr>
            <td>{{ $sku }}</td>
            <td>{{ $product->first()->product->description }}</td>
            <td>{{ $product->first()->product->zone }}</td>
            <td>@if($product->first()->product->vendor_id) {{ $product->first()->product->vendor->name }} @else - @endif
            </td>
            <td>{{ $product->first()->product->weight_start }} - {{ $product->first()->product->weight_end }}</td>
            <td>{{ $product->first()->product->product_type->name }}</td>
            <td>RM{{ number_format($product->sum('total_price'), 2, ".", "") }}</td>
        </tr>
        @endforeach
    </tbody>
</table>