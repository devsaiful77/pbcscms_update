@php
    use Modules\Requisition\App\Constants\RequisitionConstants;
@endphp
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Requisition Number</th>
                <th>Region</th>
                <th>Type</th>
                <th>Status</th>
                <th>Initiator</th>
                <th>Log Date &amp; Time</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Approved Quantity</th>
                <th>Unit Price</th>
                <th>Total Requisition Amount</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($dataForView) && count($dataForView) > 0)
                @foreach($dataForView as $data)
                    @php
                        $status = '';
                        $type = '';
                        if ($data['status'] == RequisitionConstants::REQ_PENDING){
                            $status = "Pending";
                        }elseif ($data['status'] == RequisitionConstants::REQ_DRAFT){
                            $status = "Draft";
                        }elseif ($data['status'] == RequisitionConstants::REQ_WIP){
                            $status = "WIP";
                        }elseif ($data['status'] == RequisitionConstants::REQ_APPROVE){
                            $status = "Approved";
                        }elseif ($data['status'] == RequisitionConstants::REQ_CLOSE){
                            $status = "Closed";
                        }elseif ($data['status'] == RequisitionConstants::REQ_CANCEL){
                            $status = "Cancel";
                        }

                        if ($data['type'] == RequisitionConstants::NONPRINTINGITEM){
                            $type = "Table Stationery";
                        }elseif ($data['type'] == RequisitionConstants::PRINTINGITEM){
                            $type = "Printing Stationery";
                        }elseif ($data['type'] == RequisitionConstants::REPAIRITEM){
                            $type = "Repair &amp; Maintenance";
                        }elseif ($data['type'] == RequisitionConstants::NEWASSETITEM){
                            $type = "Replacement/New Asset";
                        }
                    @endphp
                    <tr>
                        <td>
                            @if ($data['type'] == RequisitionConstants::PRINTINGITEM)
                                <a target="_blank" href="{{ route('requisition.printing-item.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                            @elseif($data['type'] == RequisitionConstants::NONPRINTINGITEM)
                                <a target="_blank" href="{{ route('requisition.table-stationery.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                            @else
                                <a target="_blank" href="{{ route('requisition.general-service.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                            @endif
                        </td>
                        <td>{{ $data['region_name'] ?? 'N/A' }}</td>
                        <td>{{ $type }}</td>
                        <td>{{ $status }}</td>
                        <td>{{ $data['created_by'] ?? '' }}</td>
                        <td>{{ $data['date_UNXTIME'] ?? '' }}</td>
                        <td>{{ $data['product_code'] ?? 'N/A' }}</td>
                        <td>{{ $data['product_name'] ?? 'N/A' }}</td>
                        <td>{{ $data['approved_quantity'] ?? 0 }}</td>
                        <td>{{ number_format($data['unit_price'] ?? 0, 2) }}</td>
                        <td>{{ number_format($data['total_requisition_amount'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="11">Data not available</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>