@php
use Modules\Requisition\App\Constants\RequisitionConstants;
use Modules\Requisition\App\Models\Vendor;
@endphp
<style>
    table.table-bordered,
    table.table-bordered th,
    table.table-bordered td {
        border: 1px solid #000 !important;
    }
</style>

<div class="table-responsive">
	<table class="table table-bordered table-condensed">
		<thead>
            <tr>
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
                <th class="text-center vcenter">Vendors</th>
                <th class="text-center vcenter">Items</th>

                <th class="text-center vcenter">Comment</th>
                <th class="text-center vcenter">Action</th>
            </tr>
		</thead>
		<tbody>

        @php
            if(!empty($dataObj)){
                $grouped = $dataObj->groupBy('requisition_number');
            }
        @endphp

        @if(isset($dataObj) && count($dataObj) > 0)
			@foreach ($grouped as $reqNo => $rows)
                @php
                    $rowspan = count($rows);
                    $first = $rows->first();

                       $items = match($first['type']) {
                            RequisitionConstants::PRINTINGITEM => explode(',', $first['ps_product_names'] ?? ''),
                            RequisitionConstants::NONPRINTINGITEM => explode(',', $first['ts_product_names'] ?? ''),
                            default => explode(',', $first['gs_product_names'] ?? '')
                        };

                        $status = '';
                        $type = '';

                        if ($first['status'] == RequisitionConstants::REQ_PENDING){
                            $status = "Pending";
                        }elseif ($first['status'] == RequisitionConstants::REQ_DRAFT){
                            $status = "Draft";
                        }elseif ($first['status'] == RequisitionConstants::REQ_WIP){
                            $status = "WIP";
                        }elseif ($first['status'] == RequisitionConstants::REQ_APPROVE){
                            $status = "Approved";
                        }elseif ($first['status'] == RequisitionConstants::REQ_CLOSE){
                            $status = "Closed";
                        }elseif ($first['status'] == RequisitionConstants::REQ_CANCEL){
                            $status = "Cancel";
                        }

                        if ($first['type'] == RequisitionConstants::NONPRINTINGITEM){
                            $type = "Table Stationery";
                        }elseif ($first['type'] == RequisitionConstants::PRINTINGITEM){
                            $type = "Printing Stationery";
                        }elseif ($first['type'] == RequisitionConstants::REPAIRITEM){
                            $type = "Repair & Maintenance";
                        }elseif ($first['type'] == RequisitionConstants::NEWASSETITEM){
                            $type = "Replacement/New Asset";
                        }
                @endphp
                @foreach($rows as $index => $data)
                    <tr>
                        @if($index == 0)
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">
                                @if ($first['type'] == RequisitionConstants::PRINTINGITEM)
                                    <a target="_blank" href="{{ route('requisition.printing-item.history', encrypt($reqNo)) }}">{{ $reqNo }}</a>
                                @elseif($first['type'] == RequisitionConstants::NONPRINTINGITEM)
                                    <a target="_blank" href="{{ route('requisition.table-stationery.history', encrypt($reqNo)) }}">{{ $reqNo }}</a>
                                @else
                                    <a target="_blank" href="{{ route('requisition.general-service.history', encrypt($reqNo)) }}">{{ $reqNo }}</a>
                                @endif
                            </td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">{{ $data['region_name'] ?? 'N/A'}}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">
                                {{ $type }}
                            </td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">{{ $status ?? ''}}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">{{ $data['created_by'] ?? ''}}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">{{ $data['i_phone'] ?? ''}}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">{{ $data['source_subgroup_name'] ?? ''}}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">{{ $data['date_UNXTIME'] ?? ''}}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">
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
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">{{ $data['receive_date'] ? 'Delivered' : 'Not Delivered' }}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">{{ $data['receive_date'] ?? 'N/A' }}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter">
                                @php
                                    $vendorNames = [];
                                    if(!empty($data['vendors_id'])){
                                        $ids = array_map('trim', explode(',', $data['vendors_id']));
                                        $vendorNames = Vendor::whereIn('id', $ids)->pluck('name')->toArray();
                                    }
                                @endphp
                                {{ !empty($vendorNames) ? implode(', ', $vendorNames) : 'N/A' }}
                            </td>
                            <td rowspan="{{ $rowspan }}" class="text-center vcenter"> {{ implode(', ', $items) ?: 'N/A' }}</td>
                        @endif

                        <td class="text-center vcenter">{{ $data['comment'] ?? ''}}</td>
                        <td class="text-center vcenter">{{ $data['action'] ?? ''}}</td>
                    </tr>
                @endforeach
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
