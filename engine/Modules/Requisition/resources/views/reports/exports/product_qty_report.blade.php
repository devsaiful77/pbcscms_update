@php

@endphp
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold; text-align: center;">#</th>
                <th style="font-weight: bold; text-align: left;">Product Name</th>
                <th style="font-weight: bold; text-align: right;">Unit Price</th>
                <th style="font-weight: bold; text-align: center;">Total Approved Qty</th>
                <th style="font-weight: bold; text-align: right;">Total Requisition Amount</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($dataForView) && count($dataForView) > 0)
                @php
                    $grandTotalQty = 0;
                    $grandTotalAmount = 0;
                @endphp
                @foreach($dataForView as $key => $data)
                    @php
                        $productName = is_array($data) ? ($data['product_name'] ?? 'N/A') : ($data->product_name ?? 'N/A');
                        $unitPrice = is_array($data) ? ($data['unit_price'] ?? 0) : ($data->unit_price ?? 0);
                        $totalQty = is_array($data) ? ($data['total_approved_qty'] ?? 0) : ($data->total_approved_qty ?? 0);
                        $totalAmount = is_array($data) ? ($data['Total_Requisition_Amount'] ?? 0) : ($data->Total_Requisition_Amount ?? 0);

                        $grandTotalQty += $totalQty;
                        $grandTotalAmount += $totalAmount;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="text-align: left;">{{ $productName }}</td>
                        <td style="text-align: right;">{{ number_format($unitPrice, 2) }}</td>
                        <td style="text-align: center;">{{ number_format($totalQty) }}</td>
                        <td style="text-align: right;">{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                @endforeach

                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($grandTotalQty) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($grandTotalAmount, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="5" style="text-align: center;">Data not available</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
