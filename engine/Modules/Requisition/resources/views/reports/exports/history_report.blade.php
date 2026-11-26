
@php
    use Modules\Requisition\App\Constants\RequisitionConstants;
    use Modules\Requisition\App\Models\Vendor;
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
            <th>Mobile Number</th>
            <th>Initiator Branch</th>
            <th>Log Date &amp; Time</th>
            <th>Current Position</th>
            <th>Delivery Status</th>
            <th>Receive Date</th>
            <th>Vendors</th>
            <th>Items</th>
            <th>Comment</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($dataForView) && count($dataForView) > 0)
            @foreach($dataForView as $data)
                @php
                    $items = match($data['type']) {
                        RequisitionConstants::PRINTINGITEM => explode(',', $data['ps_product_names'] ?? ''),
                        RequisitionConstants::NONPRINTINGITEM => explode(',', $data['ts_product_names'] ?? ''),
                        default => explode(',', $data['gs_product_names'] ?? '')
                    };

                    $vendorNames = [];
                    if(!empty($data['vendors_id'])){
                        $ids = array_map('trim', explode(',', $data['vendors_id']));
                        $vendorNames = Vendor::whereIn('id', $ids)->pluck('name')->toArray();
                    }

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
                    <td>{{ $data['i_phone'] ?? '' }}</td>
                    <td>{{ $data['source_subgroup_name'] ?? '' }}</td>
                    <td>{{ $data['date_UNXTIME'] ?? '' }}</td>
                    <td>
                        @if($data['status'] == RequisitionConstants::REQ_DRAFT)
                            {{ $data['user_id'] ?? 'N/A' }} [ REQ Initiator ]
                        @else
                            @if(!empty($data['region_id']))
                                {{ $data['region_name'] }}
                            @else
                                {{ $data['subgroup_name'] }}
                            @endif
                            @if($data['unit_id'] == 53)
                                [ REQ Admin Maker ]
                            @elseif($data['unit_id'] == 54)
                                [ REQ Admin Checker ]
                            @elseif($data['unit_id'] == 25)
                                [ REQ Initiator ]
                            @endif
                        @endif
                    </td>
                    <td>{{ $data['receive_date'] ? 'Delivered' : 'Not Delivered' }}</td>
                    <td>{{ $data['receive_date'] ?? 'N/A' }}</td>
                    <td>
                        {{ !empty($vendorNames) ? implode(', ', $vendorNames) : 'N/A' }}
                    </td>
                    <td> {{ implode(', ', $items) ?: 'N/A' }}</td>
                    <td>{{ $data['comment'] ?? ''}}</td>
                    <td>{{ $data['action'] ?? ''}}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="15">Data not available</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
