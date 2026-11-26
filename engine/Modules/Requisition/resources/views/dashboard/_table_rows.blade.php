@if($requisitions->count() > 0)
    @php $rowNumber = 1; @endphp
    @foreach($requisitions as $requisition)
        {{-- Printing Items --}}
        @foreach($requisition->printings as $item)
        <tr>
            <td class="fw-medium">{{ $rowNumber++ }}</td>
            <td>
                <a target="_blank" href="{{ route('requisition.printing-item.history', encrypt($requisition->requisition_number)) }}">{{ $requisition->requisition_number }}</a>
            </td>
            <td>{{ $requisition->created_by ?? 'N/A' }}</td>
            <td>{{ $item?->requItem?->product_name ?? 'N/A' }}</td>
            <td>{{ number_format($item->quantity ?? 0) }}</td>
            <td class="{{ ($item?->requItem?->stock?->quantity ?? 0) < ($item->quantity ?? 0) ? 'text-danger fw-bold' : '' }}">
                {{ number_format($item?->requItem?->stock?->quantity ?? 0) }}
            </td>
        </tr>
        @endforeach

        {{-- Table Stationery Items --}}
        @foreach($requisition->tableStationeries as $item)
        <tr>
            <td class="fw-medium">{{ $rowNumber++ }}</td>
            <td>
                <a target="_blank" href="{{ route('requisition.table-stationery.history', encrypt($requisition->requisition_number)) }}">{{ $requisition->requisition_number }}</a>
            </td>
            <td>{{ $requisition->created_by ?? 'N/A' }}</td>
            <td>{{ $item?->requItem?->product_name ?? 'N/A' }}</td>
            <td>{{ number_format($item->quantity ?? 0) }}</td>
            <td class="{{ ($item?->requItem?->stock?->quantity ?? 0) < ($item->quantity ?? 0) ? 'text-danger fw-bold' : '' }}">
                {{ number_format($item?->requItem?->stock?->quantity ?? 0) }}
            </td>
        </tr>
        @endforeach

        {{-- General Services Items --}}
        @foreach($requisition->generalServices as $item)
        <tr>
            <td class="fw-medium">{{ $rowNumber++ }}</td>
            <td>
                <a target="_blank" href="{{ route('requisition.general-service.history', encrypt($requisition->requisition_number)) }}">{{ $requisition->requisition_number }}</a>
            </td>
            <td>{{ $requisition->created_by ?? 'N/A' }}</td>
            <td>{{ $item?->requItem?->product_name ?? 'N/A' }}</td>
            <td>{{ number_format($item->quantity ?? 0) }}</td>
            <td>{{ number_format($item?->requItem?->stock?->quantity ?? 0) }}</td>
        </tr>
        @endforeach
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center text-muted py-5">
            <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
            No requisitions found in this date range.
        </td>
    </tr>
@endif
