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
                <th class="text-center vcenter">Requisition Number</th>
                <th class="text-center vcenter">Region</th>
                <th class="text-center vcenter">Type</th>
                <th class="text-center vcenter">Status</th>
                <th class="text-center vcenter">Initiator</th>
                <th class="text-center vcenter">Mobile Number</th>
                <th class="text-center vcenter">Initiator Branch</th>
                <th class="text-center vcenter">Log Date & Time</th>
                <th class="text-center vcenter">Current Position</th>
                <th class="text-center vcenter">Delivery Status</th>
                <th class="text-center vcenter">Receive Date</th>
                <th class="text-center vcenter">Items</th>
                {{-- <th class="text-center vcenter">Vendors</th> --}}
            </tr>
		</thead>
		<tbody>
            
		@if(!empty($dataObj) && count($dataObj) > 0)
			@foreach ($dataObj as $key => $data)
                @php
                    $items = match($data['type']) {
                        RequisitionConstants::PRINTINGITEM => explode(',', $data['ps_product_names'] ?? ''),
                        RequisitionConstants::NONPRINTINGITEM => explode(',', $data['ts_product_names'] ?? ''),
                        default => explode(',', $data['gs_product_names'] ?? '')
                    };
                    $items = array_filter($items);
                    $rowCount = max(1, count($items));

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
                        $type = "Repair & Maintenance";
                    }elseif ($data['type'] == RequisitionConstants::NEWASSETITEM){
                        $type = "Replacement/New Asset";
                    }
                @endphp
                <tr>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $loop->iteration }}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">
                        @if ($data['type'] == RequisitionConstants::PRINTINGITEM)
                            <a target="_blank" href="{{ route('requisition.printing-item.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                        @elseif($data['type'] == RequisitionConstants::NONPRINTINGITEM)
                            <a target="_blank" href="{{ route('requisition.table-stationery.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                        @else
                            <a target="_blank" href="{{ route('requisition.general-service.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                        @endif
                    </td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $data['region_name'] ?? 'N/A'}}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $type ?? ''}}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $status ?? ''}}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $data['created_by'] ?? ''}}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $data['i_phone'] ?? ''}}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $data['source_subgroup_name'] ?? ''}}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $data['date_UNXTIME'] ?? ''}}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">
                        @if($data['status'] == RequisitionConstants::REQ_DRAFT)
                            {{ $data['username'] ?? 'N/A' }} [ REQ Initiator ]
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
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $data['receive_date'] ? 'Delivered' : 'Not Delivered' }}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center vcenter">{{ $data['receive_date'] ?? 'N/A' }}</td>
                    {{-- <td class="text-center vcenter">{{ $data['vendor_name'] ?? 'N/A'}}</td> --}}
                    {{-- FIRST ITEM --}}
                    <td class="text-center vcenter">{{ $items[0] ?? 'N/A' }}</td>
                </tr>
                {{-- ADDITIONAL ROWS FOR ITEMS AND VENDORS --}}
                @for($i = 1; $i < $rowCount; $i++)
                    <tr>
                        <td class="text-center vcenter">{{ $items[$i] ?? '' }}</td>
                    </tr>
                @endfor
            @endforeach
		@else
			<tr><th class="text-center vcenter no-padding-margin-tb" colspan="19">Data not available</th></tr>
		@endif
		</tbody>
		<tfoot>
		@if(!empty($dataObj))
			@if($dataObj->total() > $dataObj->perPage())
                <tr><td class="text-right vcenter no-padding-margin-tb" colspan="19">{{ $dataObj->appends($searchDataForView)->links('vendor/pagination/default') }}</td></tr>
            @endif
        @endif
        </tfoot>
	</table>
</div>
