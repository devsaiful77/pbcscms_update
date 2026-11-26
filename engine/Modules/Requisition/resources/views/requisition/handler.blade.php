@php
use Modules\Requisition\App\Constants\RequisitionConstants;
use App\Services\UtilService;
@endphp
@extends('requisition::layouts.master')
@section('content')
<div class="mt-3">
    <form method="GET" action="{{ route('requisition.index') }}" id="handler-search">
        <div class="row mb-2">
            <div class="col-2">
                <select name="category" class="form-select">
                    <option value="">Select Type</option>
                    <option value="repair" {{ request('category') == 'repair' ? 'selected' : '' }}>Repair & Maintenance</option>
                    <option value="newAsset" {{ request('category') == 'newAsset' ? 'selected' : '' }}>Replacement/New Asset</option>
                    <option value="printingStationery" {{ request('category') == 'printingStationery' ? 'selected' : '' }}>Printing Stationery</option>
                    <option value="tableStationery" {{ request('category') == 'tableStationery' ? 'selected' : '' }}>Table Stationery</option>
                </select>
            </div>
            <div class="col-2">
                <input type="date" name="date_from" class="form-control srvDatePicker" placeholder="Log Date From" value="{{ request('date_from') }}">
            </div>
            <div class="col-2">
                <input type="date" name="date_to" class="form-control srvDatePicker" placeholder="Log Date To" value="{{ request('date_to') }}">
            </div>
            <div class="col-2">
                <select name="status[]" id="status" class="form-select select2" multiple>
                    <option value="wip" {{ in_array('wip', (array) request('status')) ? 'selected' : '' }}>WIP</option>
                    <option value="pending" {{ in_array('pending', (array) request('status')) ? 'selected' : '' }}>Pending</option>
                    <option value="draft" {{ in_array('draft', (array) request('status')) ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ in_array('approved', (array) request('status')) ? 'selected' : '' }}>Approved</option>
                    <option value="cancelled" {{ in_array('cancelled', (array) request('status')) ? 'selected' : '' }}>Cancelled</option>
                    <option value="closed" {{ in_array('closed', (array) request('status')) ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-4">
                <button style="margin-top: 2px;" type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-search"></i> Find
                </button>
                <a href="{{ route('requisition.index') }}" style="margin-top: 2px;" type="submit" class="btn btn-warning btn-sm ms-3">
                    <i class="fa fa-clear"></i> Reset
                </a>
            </div>

        </div>

        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="custom-control-inline">
                <label class="radio-inline me-2">
                    <input type="radio" name="active_tab" class="i-checks form-type" value="my_req" {{
                    $searchDataForView['active_tab'] == 'my_req' ? 'checked' : '' }}><strong>&nbsp;Handler - Queue</strong>
                </label>
                <label class="radio-inline me-2">
                    <input type="radio" name="active_tab" class="i-checks form-type" value="req_history" {{
                    $searchDataForView['active_tab'] == 'req_history' ? 'checked' : '' }}><strong>&nbsp;History</strong>
                </label>
            </div>

            <select name="history_range" id="history_range" class="form-select form-select-sm mt-2" style="width: 200px;" {{ $searchDataForView['active_tab'] != 'req_history' ? 'disabled' : '' }}>
                <option value="7"  {{ request('history_range', '7')  == '7'  ? 'selected' : '' }}>History Range 7 Days</option>
                <option value="15" {{ request('history_range') == '15' ? 'selected' : '' }}>History Range 15 Days</option>
                <option value="30" {{ request('history_range') == '30' ? 'selected' : '' }}>History Range 30 Days</option>
                <option value="60" {{ request('history_range') == '60' ? 'selected' : '' }}>History Range 60 Days</option>
                <option value="90" {{ request('history_range') == '90' ? 'selected' : '' }}>History Range 90 Days</option>
            </select>
        </div>

    <div class="card">
        <div class="card-header d-flex">
            <div class="ms-auto">
                @hasrole('requisitionChecker|requisitionSupervisor')
                    <a href="{{ route('requisition.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> New Requisition
                    </a>
                @endhasrole

                <a href="{{ route('requisition.handler-export', ['active_tab' => request('active_tab')]) }}"
                    class="btn handle_export me-1"
                    id="handlerExport">
                    <i class="fa fa-download"></i> <span></span>
                </a>


            </div>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2 mb-2">
                <div class="col-4">
                    <input type="text" name="search" class="form-control form-control-sm me-2 flex-grow-1"
                        id="search" placeholder="Search Requisition Number"
                        value="{{ request('search') }}">
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
    </form>
            <div style="overflow-x:auto; white-space:nowrap;">
                <table class ="table table-striped table-condensed">
                    @php
                        $role = Auth::user()->roles->pluck("display_name")->first();
                        $group_id = Auth::user()->user_unit->subgroup_info_id;
                        $printing_group_id = 390;
                    @endphp
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="vcenter text-center">Requisition Number</th>
                            <th class="vcenter text-center">Branch</th>
                            <th class="vcenter text-center">Logger</th>
                            <th class="vcenter text-center">Status</th>
                            <th class="vcenter text-center">Type</th>


                            <th class="vcenter text-center">In Date & Time</th>





                            <th class="vcenter text-center">Current Position</th>
                            <th class="vcenter text-center">@if($searchDataForView['active_tab'] == 'req_history') Last Work By @else Action @endif</th>

                            @if($role == 'REQ Admin Checker' && $group_id == $printing_group_id)
                                <th class="vcenter text-center">Checker Action</th>
                            @endif

                            <th class="vcenter text-center">Log Date & Time</th>
                            <th class="vcenter text-center">Duration (D:H:M:S)</th>
                            <th class="vcenter text-center">Remaining (D:H:M:S)</th>
                        </tr>
                    </thead>
                    <tbody style="word-break: break-all;">
                        @if($tblData->count() > 0)
                            @php
                                $supportServiceItem = RequisitionConstants::SUPPORTSERVICEITEM;
                            @endphp
                            @foreach($tblData as $key => $data)
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
                                    }elseif ($data['status'] == RequisitionConstants::REQ_HOLD){
                                        $status = "Hold";
                                    }

                                    $commentsTable = "";
                                    $requisitionType = "";
                                    if ($data['type'] == RequisitionConstants::NONPRINTINGITEM){
                                        $type = "Table Stationery";
                                        $commentsTable = "requisition_table_histories";
                                        $requisitionType = RequisitionConstants::NONPRINTINGITEM;
                                    }elseif ($data['type'] == RequisitionConstants::PRINTINGITEM){
                                        $type = "Printing Stationery";
                                        $commentsTable = "requisition_printing_histories";
                                        $requisitionType = RequisitionConstants::PRINTINGITEM;
                                    }elseif ($data['type'] == RequisitionConstants::REPAIRITEM){
                                        $type = "Repair & Maintenance";
                                        $commentsTable = "requisition_support_service_histories";
                                        $requisitionType = RequisitionConstants::SUPPORTSERVICEITEM;
                                    }elseif ($data['type'] == RequisitionConstants::NEWASSETITEM){
                                        $type = "Replacement/New Asset";
                                        $commentsTable = "requisition_support_service_histories";
                                        $requisitionType = RequisitionConstants::SUPPORTSERVICEITEM;
                                    }

                                    $userRoleName = "";
                                    if(Auth::user()->hasRole('requisitionChecker')){
                                        $userRoleName = "requisitionChecker";
                                    }elseif(Auth::user()->hasRole('requisitionAdminMaker')){
                                        $userRoleName = "requisitionAdminMaker";
                                    }elseif(Auth::user()->hasRole('requisitionAdminChecker')){
                                        $userRoleName = "requisitionAdminChecker";
                                    }

                                    $styleColor = "";
                                    if(empty($data['access_by']) && $data['is_sendback'] == 1){
                                        $styleColor = 'backToSourceBG-blink';
                                    }

                                    $commentsArr = (array)DB::table($commentsTable)
                                        ->select('id', 'requisition_number', 'time', 'isapproved')
                                        ->where('requisition_number', $data['requisition_number'])
                                        ->where('isapproved', '1')
                                        ->orderBy('time', 'DESC')
                                        ->first();

                                    $duration = "00:00:00:00";
                                    $remainingFormatted = "00:00:00:00";
                                    $in_time = "N/A";
                                    if(!empty($commentsArr)){
                                        $start_time = date('Y-m-d H:i:s', (int)$commentsArr['time']);
                                        $end_time = date('Y-m-d H:i:s');
                                        $in_time = $start_time;
                                        $in_time = date("Y-m-d h:i:s A", strtotime($in_time));

                                        if(Auth::user()->user_unit->unit_id == $data['unit_id'] && $status != 'Approved'){
                                            $util_service = new UtilService();
                                            $duration = $util_service->queueDurationCalculator($start_time, $end_time);

                                            $sla_time = DB::table('requisition_sla_config')
                                                ->where('role_name', $userRoleName)
                                                ->where('requisition_type', $requisitionType)
                                                ->value('sla_time');

                                            if(!empty($sla_time)){
                                                list($days, $hours, $minutes, $seconds) = explode(':', $duration);
                                                $totalDurationMinutes = ($days * 24 * 60) + ($hours * 60) + $minutes + ($seconds / 60);

                                                $remainingMinutes = $sla_time - $totalDurationMinutes;

                                                if($remainingMinutes < 0){
                                                    $remainingFormatted = "00:00:00:00";
                                                }else{
                                                    $remainingSeconds = $remainingMinutes * 60;
                                                    $days = floor($remainingSeconds / 86400);
                                                    $hours = floor(($remainingSeconds % 86400) / 3600);
                                                    $minutes = floor(($remainingSeconds % 3600) / 60);
                                                    $seconds = $remainingSeconds % 60;

                                                    $remainingFormatted = sprintf('%02d:%02d:%02d:%02d', $days, $hours, $minutes, $seconds);
                                                }
                                            }
                                        }
                                    }

                                @endphp

                                <tr class="{{ $data['is_sendback'] == 1 ? 'backToSourceBG' : '' }} {{ $styleColor }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="vcenter text-center">
                                        @php
                                            $editUrl = "";
                                            $assignUrl = "";
                                            $encryptedId = encrypt($data['requisition_number']);

                                            if ($data['type'] == RequisitionConstants::PRINTINGITEM){
                                                $editUrl = route('requisition.printing-item.edit', $encryptedId);
                                                $assignUrl = route('requisition.printing.assign', $encryptedId);
                                            } elseif ($data['type'] == RequisitionConstants::NONPRINTINGITEM){
                                                $editUrl = route('requisition.table-stationery.edit', $encryptedId);
                                                $assignUrl = route('requisition.table-stationery.assign', $encryptedId);
                                            } else {
                                                $editUrl = route('requisition.general-service.edit', $encryptedId);
                                                $assignUrl = route('requisition.general-service.assign', $encryptedId);
                                            }
                                        @endphp


                                        @if($data['is_sendback'] == 1
                                            && $searchDataForView['active_tab'] !== 'req_history'
                                            && !empty($data['access_by'])
                                            && $data['access_by'] == Auth::user()->user_id)
                                            <a href="{{ url($editUrl) }}" class="" title="Edit" escape="false"> {{ $data['requisition_number'] }}</a>

                                        @else
                                            @if($searchDataForView['active_tab'] == 'req_history')
                                                @if ($data['type'] == RequisitionConstants::PRINTINGITEM)
                                                    <a target="_blank" href="{{ route('requisition.printing-item.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                @elseif($data['type'] == RequisitionConstants::NONPRINTINGITEM)
                                                    <a target="_blank" href="{{ route('requisition.table-stationery.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                @else
                                                    <a target="_blank" href="{{ route('requisition.general-service.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                @endif
                                            @else
                                                @if($data['is_sendback'] == 10)
                                                    @if ($data['type'] == RequisitionConstants::PRINTINGITEM)
                                                        <a target="_blank" href="{{ route('requisition.printing-item.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                    @elseif($data['type'] == RequisitionConstants::NONPRINTINGITEM)
                                                        <a target="_blank" href="{{ route('requisition.table-stationery.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                    @else
                                                        <a target="_blank" href="{{ route('requisition.general-service.history', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                    @endif
                                                @else
                                                    @if ($data['type'] == RequisitionConstants::PRINTINGITEM)
                                                        <a target="_blank" href="{{ route('requisition.printing-item.details', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                    @elseif($data['type'] == RequisitionConstants::NONPRINTINGITEM)
                                                        <a target="_blank" href="{{ route('requisition.table-stationery.details', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                    @else
                                                        <a target="_blank" href="{{ route('requisition.general-service.details', encrypt($data['requisition_number'])) }}">{{ $data['requisition_number'] }}</a>
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td class="vcenter text-center"> {{ $data['subgroup']['name'] ?? 'N/A' }} </td>
                                    <td class="vcenter text-center"> {{ $data['user']['user_id'] }} </td>
                                    <td class="vcenter text-center"> {{ $status }}</td>
                                    <td class="vcenter text-center">
                                        {{ $type }}
                                    </td>
                                    <td class="vcenter text-center">{{ $in_time }}</td>
                                    <td class="vcenter text-center">
                                        @if($data['status'] == RequisitionConstants::REQ_DRAFT)
                                            {{ $data['user']['user_id'] ?? 'N/A' }} [ REQ Initiator ]
                                        @else
                                            @if(!empty($data['region_id']))
                                                {{ $data['region_name'] }}
                                            @else
                                                {{ $data['sub_group_name'] }}
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
                                    <td class="vcenter text-center">

                                        @if($data['status'] == 2)
                                            @if($searchDataForView['active_tab'] !== 'req_history')
                                                <a href="{{ $editUrl }}" class="btn btn-success btn-sm" title="Edit" escape="false"> <i class="fa fa-pencil"></i> Edit</a>
                                                <a href="{{ route('requisition.publish', encrypt($data['requisition_number'])) }}" class="btn btn-primary btn-sm gradient ajax_page" title="Edit" escape="false"> <i class="fa fa-upload"></i> Submit</a>
                                            @endif
                                        @elseif($data['is_sendback'] == 1)
                                            @if($searchDataForView['active_tab'] !== 'req_history')
                                                @if(empty($data['access_by']))
                                                    <a href="{{ $assignUrl }}" class="btn btn-primary btn-sm">Assign</a>
                                                @else
                                                    @if($data['access_by'] == Auth::user()->user_id)
                                                        <a href="{{ url($editUrl) }}" class="btn btn-warning btn-sm" title="Edit" escape="false"> <i class="fa fa-pencil"></i> Modify</a>
                                                    @else
                                                        <span class="badge rounded-pill bg-success">assigned to {{ $data['access_by'] }}</span>
                                                    @endif
                                                @endif
                                            @endif
                                        @else
                                            @if(!empty($data['access_by']) && $data['status'] !== 10)
                                                <span class="badge rounded-pill bg-success" style="font-size: 14px;">assigned to {{ $data['access_by'] }}</span>
                                            @endif
                                        @endif
                                    </td>

                                    @if($userRoleName == "requisitionAdminChecker" && $group_id == $printing_group_id)
                                        <td>
                                            @if(empty($data['access_by']))
                                                <a href="javascript:void(0)"
                                                class="btn btn-sm btn-primary quick-action-btn"
                                                data-requisition="{{ $data['requisition_number'] }}"
                                                data-action="approve">
                                                Approve
                                                </a>
                                                <a href="javascript:void(0)"
                                                class="btn btn-sm btn-danger quick-action-btn"
                                                data-requisition="{{ $data['requisition_number'] }}"
                                                data-action="reject">
                                                Reject
                                                </a>
                                            @endif
                                        </td>
                                    @endif

                                    <td class="vcenter text-center"> {{  $data['date_UNXTIME'] }}</td>
                                    <td class="vcenter text-center">{{ $duration }}</td>
                                    <td class="vcenter text-center">{{ $remainingFormatted }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="vcenter text-center" colspan="12  "> <strong>Data Not Available</strong></td>
                             </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {{ $tblData->appends(request()->query())->links() }}
            @if(request()->has('search'))
                <p class="text-muted">
                    Showing {{ $tblData->total() }} result(s)
                </p>
            @endif
        </div>
    </div>
</div>

<script>
    $(".form-type").on('ifChecked', function (event) {
        const selectedTab = $(this).val();

        // Enable/disable history range dropdown based on selected tab
        if (selectedTab === 'req_history') {
            $('#history_range').prop('disabled', false);
        } else {
            $('#history_range').prop('disabled', true);
        }

        $('form#handler-search').submit();
    });

    // Auto-submit form when history range changes
    $('#history_range').on('change', function () {
        $('form#handler-search').submit();
    });

    $(document).ready(function() {
        const pageParams = new URLSearchParams(window.location.search);
        const activeTab = pageParams.get('active_tab');

        if(activeTab === 'my_req' || activeTab === null){
            $("#handlerExport").removeClass("btn-success").addClass("btn-danger").find("span").text("Export Handler");
        } else if(activeTab === 'req_history'){
            $("#handlerExport").removeClass("btn-danger").addClass("btn-success").find("span").text("Export History");
        }
    });


    $(document).ready(function () {
        $(document).on('click', '.quick-action-btn', function () {
            const requisitionNo = $(this).data('requisition');
            const action = $(this).data('action'); // approve | reject
            const defaultComment = action === 'approve' ? 'Approved' : 'Send Back';

            const comment = window.prompt(
                (action === 'approve' ? 'Approve' : 'Reject') + ` Requisition No: ${requisitionNo}\nEnter comment:`,
                defaultComment
            );

            if (comment === null) {
                return;
            }
            if (comment.trim() === '') {
                alert('Comment is required');
                return;
            }

            const form = $('<form>', {
                method: 'POST',
                action: "{{ route('requisition.printing.quickAction') }}"
            });

            form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
            form.append($('<input>', { type: 'hidden', name: 'requisition_number', value: requisitionNo }));
            form.append($('<input>', { type: 'hidden', name: 'action', value: action }));
            form.append($('<input>', { type: 'hidden', name: 'comments', value: comment.trim() }));

            $('body').append(form);
            form.submit();
        });
    });

</script>

@endsection
