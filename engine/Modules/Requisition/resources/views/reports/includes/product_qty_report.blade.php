@php
    use Modules\Requisition\App\Constants\RequisitionConstants;
@endphp
<style>
    table.table-bordered,
    table.table-bordered th,
    table.table-bordered td {
        border: 1px solid #000 !important;
        white-space: nowrap;
    }
</style>

<div class="table-responsive">
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th class="text-center vcenter">#</th>
                <th class="text-center vcenter">Product Name</th>
                <th class="text-center vcenter">Unit Price</th>
                <th class="text-center vcenter">Total Approved Qty</th>
                <th class="text-center vcenter">Total Requisition Amount</th>
            </tr>
        </thead>
        <tbody>

        @if(!empty($dataObj) && count($dataObj) > 0)
            @php
                $grandTotalQty = 0;
                $grandTotalAmount = 0;
            @endphp
            @foreach ($dataObj as $key => $data)
                @php
                    $productName = is_array($data) ? ($data['product_name'] ?? 'N/A') : ($data->product_name ?? 'N/A');
                    $unitPrice = is_array($data) ? ($data['unit_price'] ?? 0) : ($data->unit_price ?? 0);
                    $totalQty = is_array($data) ? ($data['total_approved_qty'] ?? 0) : ($data->total_approved_qty ?? 0);
                    $totalAmount = is_array($data) ? ($data['Total_Requisition_Amount'] ?? 0) : ($data->Total_Requisition_Amount ?? 0);

                    $grandTotalQty += $totalQty;
                    $grandTotalAmount += $totalAmount;
                @endphp
                <tr>
                    <td class="text-center vcenter">
                        {{ method_exists($dataObj, 'perPage') ? ($dataObj->currentpage() - 1) * $dataObj->perpage() + $loop->iteration : $loop->iteration }}
                    </td>
                    <td class="text-left vcenter">{{ $productName }}</td>
                    <td class="text-right vcenter">{{ number_format($unitPrice, 2) }}</td>
                    <td class="text-center vcenter">{{ number_format($totalQty) }}</td>
                    <td class="text-right vcenter">{{ number_format($totalAmount, 2) }}</td>
                </tr>
            @endforeach

            {{-- মোট বা Total Row --}}
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="3" class="text-right vcenter">Total:</td>
                <td class="text-center vcenter">{{ number_format($grandTotalQty) }}</td>
                <td class="text-right vcenter">{{ number_format($grandTotalAmount, 2) }}</td>
            </tr>
        @else
            <tr>
                <th class="text-center vcenter no-padding-margin-tb" colspan="5">Data not available</th>
            </tr>
        @endif
        </tbody>
        <tfoot>
        @if(!empty($dataObj) && method_exists($dataObj, 'total'))
            @if($dataObj->total() > $dataObj->perPage())
                <tr>
                    <td class="text-right vcenter no-padding-margin-tb" colspan="5">
                        {{ $dataObj->appends(request()->query())->links('vendor/pagination/default') }}
                    </td>
                </tr>
            @endif
        @endif
        </tfoot>
    </table>
</div>
