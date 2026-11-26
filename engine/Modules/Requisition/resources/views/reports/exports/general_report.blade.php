@php
    use Modules\Requisition\App\Constants\RequisitionConstants;
@endphp
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Requisition Number</th>
                <th>Region</th>
                <th>Type</th>
                <th>Status</th>
                <th>Initiator</th>
                <th>Mobile Number</th>
                <th>Initiator Branch</th>
                <th>Log Date</th>
                <th>Time</th>
                <th>Current Position</th>
                <th>Delivery Status</th>
                <th>Receive Date</th>
                <th>Vendors</th>
                <th>Items (Qty, Price, Total)</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($dataForView) && count($dataForView) > 0)
                @foreach($dataForView as $data)
                    @php
                        $status = '';
                        $type = '';
                        $itemsArray = [];
                        $grandTotal = 0;

                        // define status
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

                        // type & data explod logic 
                        if ($data['type'] == RequisitionConstants::NONPRINTINGITEM){
                            $type = "Table Stationery";
                            $itemsArray = $data['ts_product_details'] ? explode(' | ', $data['ts_product_details']) : [];
                            $grandTotal = $data['ts_grand_total'] ?? 0;
                        }elseif ($data['type'] == RequisitionConstants::PRINTINGITEM){
                            $type = "Printing Stationery";
                            $itemsArray = $data['ps_product_names'] ? explode(' | ', $data['ps_product_names']) : [];
                            $grandTotal = $data['ps_grand_total'] ?? 0;
                        }elseif ($data['type'] == RequisitionConstants::REPAIRITEM){
                            $type = "Repair &amp; Maintenance";
                            $itemsArray = $data['gs_product_names'] ? explode(', ', $data['gs_product_names']) : [];
                        }elseif ($data['type'] == RequisitionConstants::NEWASSETITEM){
                            $type = "Replacement/New Asset";
                            $itemsArray = $data['gs_product_names'] ? explode(', ', $data['gs_product_names']) : [];
                        }
                    @endphp

                    
                    <tr>
                        @php
                            $parts = explode('&', $data['date_UNXTIME']);
                            $datePart = trim($parts[0] ?? '');
                            $timePart = trim($parts[1] ?? '');
                        @endphp
                        <td>{{ $data['requisition_number'] }}</td>
                        <td>{{ $data['region_name'] ?? 'N/A' }}</td>
                        <td>{{ $type }}</td>
                        <td>{{ $status }}</td>
                        <td>{{ $data['created_by'] ?? '' }}</td>
                        <td>{{ $data['i_phone'] ?? '' }}</td>
                        <td>{{ $data['source_subgroup_name'] ?? '' }}</td>
                        <td>{{ $datePart }}</td>
                        <td>{{ $timePart }}</td>
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
                        <td>{{ $data['vendor_name'] ?? 'N/A' }}</td>
                        <td>
                            @if(!empty($itemsArray))
                                @foreach($itemsArray as $item)
                                    {{ $item }} <br>
                                @endforeach
                                @if($grandTotal > 0)
                                    <strong>Grand Total: {{ number_format($grandTotal, 2) }} TK</strong>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="13">Data not available</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
