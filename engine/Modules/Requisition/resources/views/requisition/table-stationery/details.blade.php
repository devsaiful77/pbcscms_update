@php use Modules\Requisition\App\Constants\RequisitionConstants; @endphp
@extends('requisition::layouts.master')
@section('content')
    <style>
        .jconfirm-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .col-lg-2 {
            flex: 0 0 auto;
            width: 13%;
            padding: 0.25rem;
        }
        .col-lg-3 {
            flex: 0 0 auto;
            width: 20%;
            padding: 0.25rem;
        }
        .row.here{
            textarea,select{
                /* margin-top: 0.5rem; */
                width: 100%;
                height: 3rem;
                /* margin-left: 1rem; */
                margin-right: 0.5rem;
                appearance: auto;
            }
            button{

                width: 100%;
                height: 2.2rem;
                /* margin-right: 0.5rem; */
                /* margin-top: 0.5rem; */
                font-size: 0.9rem;
            }
            .select2-container--default .select2-selection--single{
                height: 2.2rem;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 35px;
            }
            .form-control.is_justified{
                width: 180px;
                margin-right: 0rem;
                margin-top: 0.5rem;
            }
            .btn {
                --bs-btn-padding-x: 0rem !important;
                --bs-btn-padding-y: 0rem !important;
            }
            .assign{
                width: 100%;
                height: 2.2rem;
                /* margin-right: 0.5rem; */
                /* margin-top: 0.5rem; */
                font-size: 1rem;
                padding-top: 0.25rem;
                /* padding-bottom: 0.5rem; */
            }
        }

        div.scheduler-border {
            font-size: 12px;
            border-bottom-right-radius: 5px;
            border-bottom-left-radius: 5px;
        }
        fieldset.scheduler-border {
            border-radius: 5px;
        }


    </style>

    <div class="form-element-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="form-element-list">
                <div class="curved-inner-pro pt-2 mb-3" style="background-color: #DFF0D8;">
                    <div class="curved-inner-pro print-area">
                        <div class="curved-ctn">
                            <h2 class="p-2">Table Stationery - Requisition Details</h2>
                        </div>
                    </div>
                </div>
<div>
    <fieldset class="scheduler-border print-area" style="background-color:#ffff">
        <div class="scheduler-border">
            <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                Initiator Information <i class="fa-plus fa" aria-hidden="false"></i>
            </a>
        </div>
        <div class="table-responsive in collapse show" id="collapseOne" style="">
            <table class="table table-condensed table-bordered no-padding-margin-b">
                @php
                    $subgroupList = $requisition->i_branch_id;
                    $subgroupName = "";
                    $subgroupAddress = "";

                    $subgroup = DB::table('subgroup_info')->whereIn('id', explode(',', $subgroupList))->get(['name', 'address']);
                    $subgroupName    = $subgroup->pluck('name')->implode(', ');
                    $subgroupAddress = $subgroup->pluck('address')->implode(', ');
                @endphp
                <tbody>
                <tr>
                    <th class="vcenter text-left">Phone Number</th>
                    <td class="vcenter text-left">{{ $requisition->i_phone }}</td>
                    <th class="vcenter text-left">Branch Name</th>
                    <td class="vcenter text-left">{{ $subgroupName }}</td>
                    <th class="vcenter text-left">Address</th>
                    <td class="vcenter text-left">{{ $subgroupAddress }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <fieldset class="scheduler-border print-area" style="background-color:#ffff">
        <div class="scheduler-border">
            <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                Information <i class="fa-plus fa" aria-hidden="false"></i>
            </a>
        </div>
        <div class="table-responsive in collapse show" id="collapseOne" style="">
            <table class="table table-condensed table-bordered no-padding-margin-b">
                <tbody>
                <tr>
                    <th class="vcenter text-left">Requisition No.</th>
                    <td class="vcenter text-left">{{ $requisition->requisition_number }}</td>
                    <th class="vcenter text-left">Logged Date</th>
                    <td class="vcenter text-left">{{ $requisition->created_at->format('d-m-Y h:s:i A') }}</td>
                </tr>
                <tr>
                    <th class="vcenter text-left">Logged Form.</th>
                    <td class="vcenter text-left">{{ $requisition->created_by }} [{{ $requisition?->user?->name }}]</td>
                    <th class="vcenter text-left">Current Position.</th>
                    <td class="vcenter text-left">
                        @if($requisition['status'] == RequisitionConstants::REQ_DRAFT)
                            {{ $requisition['user']['user_id'] ?? 'N/A' }} [ REQ Initiator ]
                        @else
                            @if(!empty($requisition['region_id']))
                                {{ $requisition['region_name'] }}
                            @else
                                {{ $requisition['sub_group_name'] }}
                            @endif
                            @if($requisition['unit_id'] == 53)
                                [ REQ Admin Maker ]
                            @elseif($requisition['unit_id'] == 54)
                                [ REQ Admin Checker ]
                            @elseif($requisition['unit_id'] == 25)
                                [ REQ Initiator ]
                            @endif
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <form action="{{ route('requisition.workingOnTableREQ') }}" method="POST" class="mb-2">
        @csrf
        <input type="hidden" name="requisition_no" value="{{ $requisition->requisition_number }}">
        <fieldset class="scheduler-border print-area" style="background-color:#ffff">
            <div class="scheduler-border">
                <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                    Product List <i class="fa-plus fa" aria-hidden="false"></i>
                </a>
            </div>
            <div class="table-responsive collapse show" id="collapseThree" style="">
                <br>

                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th width="5%">SL.</th>
                            <th width="40%">Product List</th>
                            <th width="10%">Unit Price</th>
                            @hasanyrole('requisitionAdminMaker|requisitionAdminChecker|requisitionSupervisor')
                                <th width="15%">Stock Qty</th>
                            @endhasrole
                            <th width="15%">Request Qty</th>
                            <th width="15%">Approve Qty</th>
                        </tr>
                    </thead>

                    <tbody class="table-group-divider">
                        @foreach($requisition->tableStationeries as $key => $item)
                            <tr>
                                <th>{{ $key + 1 }}</th>
                                <td>{{ $item?->requItem?->product_name }}</td>
                                <td>{{ $item?->requItem?->unit_price }}</td>

                                @hasanyrole('requisitionAdminMaker|requisitionAdminChecker|requisitionSupervisor')
                                    <td>N /A</td>
                                @endhasrole

                                <td>
                                    {{ $item->quantity ?: 'N / A' }}
                                </td>

                                <td>
                                    @php
                                        // ইউজারের রোলগুলো একবারই চেক করে ভেরিয়েবলে রাখছি পারফরম্যান্সের জন্য
                                        $isMaker = auth()->user()->hasRole('requisitionAdminMaker');
                                        $isSupervisor = auth()->user()->hasRole('requisitionSupervisor');
                                        $isAdminChecker = auth()->user()->hasRole('requisitionAdminChecker');

                                        $showInputField = $isMaker || ($isSupervisor && $requisition->unit_id == 53);

                                        $showValueText = $isAdminChecker || ($isSupervisor && $requisition->unit_id == 54);
                                    @endphp

                                    @if($showInputField)
                                        <input type="number"
                                            name="approve_qty[{{ $item->id }}][]"
                                            value="{{ $item->approve_qty == 0 ? $item->quantity : $item->approve_qty }}">

                                    @elseif($showValueText)
                                        {{ $item->approve_qty ?: 'N/A' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </fieldset>
        <fieldset class="scheduler-border" style="background-color:#ffff">
          <div class="scheduler-border">
              <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                  Requisition History <i class="fa-plus fa" aria-hidden="false"></i>
              </a>
          </div>
          <div class="table-responsive collapse show" id="collapseFour" style="">
              <br>
                    <table class="table table-condensed table-bordered no-padding-margin-b">

                  </table>
                  <table style="width: 100%; margin: 0 auto; border-collapse: separate;" border="0">
                      <tbody>
                            <tr>
                                <td class="topandbottom" style="border:1px solid #9acd32; font-weight: bold; font-family: serif;padding-left: 5px;">Person</td>
                                <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; padding-bottom:5px; border-top:1px solid #9acd32; border-bottom:1px solid #9acd32; font-weight: bold; font-family: serif;padding-left: 5px;">Action</td>
                                <td class="topandbottom" style="border:1px solid #9acd32; font-weight: bold; font-family: serif;padding-left: 5px;">Date & Time</td>
                                <td class="topandbottom" style="padding-top:5px; padding-bottom:5px; border-right:1px solid #9acd32;  border-top:1px solid #9acd32; border-bottom:1px solid #9acd32; font-weight: bold; font-family: serif;padding-left: 5px;">Comment</td>
                            </tr>

                            @foreach($requisition->tableRequHistories as $comment)
                            <tr>
                                <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border-bottom:1px solid #9acd32; border-left:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">{{ $comment->user_id }} [{{ $comment?->user?->name }}]</td>
                                <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border-bottom:1px solid #9acd32; border-left:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">{{ $comment->action }}</td>
                                <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border-bottom:1px solid #9acd32; border-left:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">{{ date("d.m.Y ## h:i a", $comment->time)}}</td>
                                <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border-bottom:1px solid #9acd32; border-left:1px solid #9acd32; border-right:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">{{ $comment->comment }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                  <table>
            </table>
          </div>
      </fieldset>
        <fieldset class="scheduler-border print-area" style="background-color:#ffff">
            <div class="scheduler-border">
                <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                    Comments <i class="fa-plus fa" aria-hidden="false"></i>
                </a>
            </div>
            <div class="table-responsive collapse show" id="collapseFive" style="">
                <table class="table">
                    <thead>
                    <tr>
                        <th width="20%"></th>
                        <th width="80%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>Specification: </strong></td>
                        <td>{{ $requisition->specification }}</td>
                    </tr>
                    <tr>
                        <td><strong>Justification: </strong></td>
                        <td>{{ $requisition->justification }}</td>
                    </tr>
                    <tr>
                        <td><strong>Remarks: </strong></td>
                        <td>{{ $requisition->remark }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
        <fieldset class="scheduler-border" style="background-color:#ffff">
            <div class="scheduler-border">
                <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="true" aria-controls="collapseSix" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                    Attachments <i class="fa-plus fa" aria-hidden="false"></i>
                </a>
            </div>
            <div class="row mb-2 collapse show" id="collapseSix">
                <div class="col-md-6">
                    <table class="table table-condensed text-nowrap">
                        <tr>
                            <p style="background: #e4def5; text-align:center"><b>Admin Attachments</b></p>
                        </tr>
                        <thead>
                            <tr>
                                <th width="30%">Title</th>
                                <th width="25%">Item Name</th>
                                <th width="30%">Attachment</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->requAttachments as $attach)

                                @if ($attach->is_admin_attach == 1)
                                    <tr>
                                        <td>{{ $attach->attachment_title }}</td>

                                        <td>
                                            {{ $attach->requisitionItem?->product_name ?? 'N/A' }}
                                        </td>

                                        <td>
                                            <a href="{{ URL::asset('public/attachments/'.$attach->attachment) }}"
                                            target="_blank">
                                                {{ $attach->attachment }}
                                            </a>
                                        </td>

                                        <td>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm delete-attachment"
                                                    data-id="{{ $attach->id }}"
                                                    data-name="{{ $attach->attachment_title }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table table-condensed text-nowrap">
                        <tr>
                            <p style="background: #e4def5; text-align:center"><b>Initiator Attachments</b></p>
                        </tr>
                        <thead>
                            <tr>
                                <th width="50%">Title</th>
                                <th width="50%">Attachment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->requAttachments as $attach)
                                @if ($attach->is_admin_attach != 1)
                                    <tr>
                                        <td><span>{{ $attach->attachment_title }} </span></td>
                                        <td><a href="{{URL::asset('public/attachments/'.$attach->attachment)}}" target="_blank">{{$attach->attachment}}</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </fieldset>
        @if ($vendors_info->isNotEmpty())
            @if (in_array(53, userUnits()) || in_array(54, userUnits()) || $isSupervisor)
                <fieldset class="scheduler-border" style="background-color:#ffff">
                    <div class="scheduler-border">
                        <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="true" aria-controls="collapseEight" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                            Vendor Information <i class="fa-plus fa" aria-hidden="false"></i>
                        </a>
                    </div>
                    <div class="table-responsive collapse show" id="collapseEight" style="">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr class="text-center">
                                    <th>Vendor Name</th>
                                    <th>Vendor Email</th>
                                    <th>Vendor Phone</th>
                                    <th>Address</th>
                                    <th>Mail Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    @foreach ($vendors_info as $item)
                                        <tr class="text-center">
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{{ $item->office_address }}</td>
                                            <td>
                                                @if (isset($vendor_mail_status[$item->id]))
                                                    @if ($vendor_mail_status[$item->id] == 3)
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($vendor_mail_status[$item->id] == 7)
                                                        <span class="badge bg-success">Sent</span>
                                                    @elseif($vendor_mail_status[$item->id] == -1)
                                                        <span class="badge bg-danger">Failed</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            @endif
        @endif

        @if(Auth::user()->user_id == $requisition->access_by && ($requisition->status != RequisitionConstants::REQ_CLOSE && $requisition->status != RequisitionConstants::REQ_CANCEL))
            @php
                $userUnitIds = explode(',', Auth::user()->user_unit->unit_id ?? '');
                $hasValidUnit = in_array('53', $userUnitIds) || in_array('33', $userUnitIds);
            @endphp
            @if ($vendors_info->isEmpty() || $hasValidUnit)

                @if (Auth::user()->user_id == $requisition->access_by && $hasValidUnit)
                    <fieldset class="scheduler-border" style="background-color:#ffff">
                        <div class="scheduler-border">
                            <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                                Vendor Assignment <i class="fa-plus fa" aria-hidden="false"></i>
                            </a>
                        </div>
                        <div class="col-4 mb-3">
                            <select class="form-select" id="vendorSelect" multiple>
                                <option value="">Select Vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                            @error('no_vendor_error')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="table-responsive collapse show d-none" id="collapseEight">
                            <table class="table table-bordered table-condensed" id="vendorDetailsTable">
                                <thead>
                                    <tr class="text-center">
                                        <th>Vendor Name</th>
                                        <th>Vendor Email</th>
                                        <th>Vendor Phone</th>
                                        <th>Address</th>
                                        <th>Product</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </fieldset>
                @endif
            @endif

            @hasanyrole('requisitionAdminMaker|requisitionAdminChecker|requisitionSupervisor')
                <fieldset class="scheduler-border" style="background-color:#ffff">
                    <div class="scheduler-border">
                        <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="true" aria-controls="collapseNine" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                            Attachment Upload <i class="fa-plus fa" aria-hidden="false"></i>
                        </a>
                    </div>
                    <div id="attachmentForm" enctype="multipart/form-data">
                        <div class="row g-3">
                            <input type="hidden" name="requisition_number" value="{{ $requisition->requisition_number }}">
                            <div class="col-md-4">
                                <label class="form-label">Choose Item</label>
                                <select class="form-select" name="item_id">
                                    <option value="">Select Item</option>
                                    @foreach($requisition->tableStationeries as $key => $item)
                                        <option value="{{ $item?->requItem?->id }}">{{ $item?->requItem?->product_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="item_error"></span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Attachment Title</label>
                                <input type="text" name="attachment_title" class="form-control" placeholder="Attachment Title">
                                <span class="text-danger" id="title_error"></span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="attachment" id="attachmentInput">
                                <span class="text-danger" id="attachment_error"></span>
                            </div>

                            <div class="col-12 mb-4">
                                <button type="button" class="btn btn-success" id="uploadBtn">Upload</button>
                            </div>
                        </div>
                    </div>
                </fieldset>
            @endhasanyrole
        @endif
        <button type="button" class="btn btn-info btn-sm float-end" onclick="printByClass('print-area')"><i class="fa fa-print"></i> Print This Page</button>
        <div>
            @if($requisition->status == RequisitionConstants::REQ_APPROVE)
                @if (in_array(25, userUnits()) || in_array(53, userUnits()) || $isSupervisor)
                    <div class="col-5">
                        <label class="form-label" for="receive_date">Receive Date <span class="text-danger">*</span></label>
                        <input type="date" id="receive_date" name="receive_date" class="form-control srvDatePicker mb-2" placeholder="Receive Date">
                        @if($errors->has('receive_date'))
                            <div class="error-message">{{ $errors->first('receive_date') }}</div>
                        @endif
                    </div>
                    <div class="col-5">
                        <label class="form-label" for="comments">Comment <span class="text-danger">*</span></label>
                        <textarea class="form-control mb-2" name="comments" id="comments" rows="5" placeholder="Enter a closing comment here..."></textarea>
                        @if($errors->has('comments'))
                            <div class="error-message">{{ $errors->first('comments') }}</div>
                        @endif
                        <button type="submit" name="action" value="close" class="btn btn-danger mt-2" onclick="overlay('show');">
                            <i class="fa fa-lock"></i> Close This Requisition</button>
                    </div>
                @endif
            @endif
            @if($actionAccess)
                @if(empty($requisition->access_by))
                    @if(in_array($requisition->unit_id,userUnits()) || $isSupervisor)
                        <a href="{{ route('requisition.table-stationery.assign', encrypt($requisition->requisition_number)) }}" class="btn btn-primary">Assign</a>
                    @endif
                @else
                    @if(Auth::user()->user_id == $requisition->access_by
                        && ($requisition->status != RequisitionConstants::REQ_CANCEL
                        && $requisition->status != RequisitionConstants::REQ_CLOSE))
                        <div class="mb-3 col-5">
                            <label for="comments" class="form-label">Comment</label>
                            <textarea name="comments" id="comments" rows="3" class="form-control" placeholder="Enter your comment here...">{{ old('comments') }}</textarea>
                            @if($errors->has('comments'))
                                <div class="error-message">{{ $errors->first('comments') }}</div>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="sendback" class="btn-sm btn btn-warning" onclick="overlay('show');">Back To Source</button>
                            @if($requisition->unit_id == 53)
                                <button type="submit" name="action" value="approve_forward" class="btn-sm btn btn-success mx-3" onclick="overlay('show');">Approve & Forward</button>
                            @else
                                <button type="submit" name="action" value="approve" class="btn-sm btn btn-success" onclick="overlay('show');">Approve</button>
                            @endif
                            <button type="submit" name="action" value="hold" class="btn-sm btn btn-primary" onclick="overlay('show');">Hold</button>
                            <button type="submit" name="action" value="cancel" class="btn-sm btn btn-secondary" onclick="overlay('show');">Cancel</button>
                        </div>
                    @endif
                @endif
            @endif

        </div>
    </form>
@endsection

@push('scripts')
    <script>
        var vendorsId = [];
        var vendorId = "";
        $(document).ready(function () {
            $('#uploadBtn').on('click', function (e) {
                // Prevent outer form submission
                e.preventDefault();

                $('#title_error').text('');
                $('#attachment_error').text('');

                let formData = new FormData();
                formData.append('requisition_number', $('input[name="requisition_number"]').val());
                formData.append('attachment_title', $('input[name="attachment_title"]').val());
                formData.append('item_id', $('select[name="item_id"]').val());
                let fileInput = $('#attachmentInput')[0]; // Add id="attachmentInput" to input
                if (fileInput.files.length > 0) {
                    formData.append('attachment', fileInput.files[0]);
                }

                $.ajax({
                    url: "{{ route('requisition.single_attach_upload') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        window.location.reload(true);
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.attachment_title) {
                                $('#title_error').text(errors.attachment_title[0]);
                            }
                            if (errors.attachment) {
                                $('#attachment_error').text(errors.attachment[0]);
                            }
                            if (errors.item_id) {
                                $('#item_error').text(errors.item_id[0]);
                            }
                        } else {
                            alert('Something went wrong. Please try again.');
                        }
                    }
                });
            });


            // Delete Attachment
            $(document).on('click', '.delete-attachment', function () {
                let attachId = $(this).data('id');
                let attachName = $(this).data('name');
                let $row = $(this).closest('tr');

                $.confirm({
                    title: 'Confirm Delete!',
                    content: `Are you sure you want to delete the attachment "<b>${attachName}</b>"?`,
                    type: 'red',
                    buttons: {
                        confirm: {
                            text: 'Delete',
                            btnClass: 'btn-danger',
                            action: function () {
                                $.ajax({
                                    url: `/requisition/attachment/delete/${attachId}`,
                                    type: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    success: function (response) {
                                        if (response.success) {
                                            $row.fadeOut(300, function () {
                                                $(this).remove();
                                            });
                                        }
                                    },
                                    error: function () {
                                        alert('Unable to delete. Please try again.');
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: 'Cancel'
                        }
                    }
                });
            });


            $('#vendorSelect').select2({
                placeholder: "Select Vendor",
                width: '100%',
            });

            $('#vendorSelect').on('change', function () {
                let id = $(this).val();
                vendorId = id;
                if(id.length == 0){
                    $('#collapseEight').addClass('d-none');
                }
                if (id.length > 0) {
                    $.ajax({
                        url: "{{ route('requisition.general-service.vendor.details') }}",
                        type: 'GET',
                        data: { vendorsId: id },
                        success: function (data) {
                            let html = '';

                            data.data.forEach(function(item, index) {
                                html += `
                                    <tr class="text-center" data-vendor-id="${item.id}">
                                        <td>${item.name}</td>
                                        <td>${item.email}</td>
                                        <td>${item.phone}</td>
                                        <td>${item.address}</td>
                                        <td>
                                            <input type="hidden" name="vendor_data[${index}][vendor_id]" value="${item.id}">
                                            <select style="width:250px;" name="vendor_data[${index}][product_id][]" class="form-select productSelect" multiple>
                                                <option value="">Select Product</option>
                                                @foreach($requisition->tableStationeries as $key => $item)
                                                    <option value="{{ $item?->requItem?->id }}">{{ $item?->requItem?->product_name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="product-validation-msg text-danger d-block" style="font-size:0.85rem;"></span>
                                        </td>
                                    </tr>
                                `;
                            });

                            $('#vendorDetailsTable tbody').html(html);

                            setTimeout(function () {
                                $('.productSelect').select2({
                                    placeholder: "Select Product",
                                    width: 'style',
                                });

                                updateProductOptions();
                            }, 0);

                            $('#collapseEight').removeClass('d-none');
                            vendorsId = id;
                        },
                        error: function () {
                            $('#collapseEight').addClass('d-none');
                            alert('Failed to fetch vendor details.');
                        }
                    });
                } else {
                    $('#vendorDetailsTable tbody').html('<tr><td colspan="4" class="text-center">No vendor selected</td></tr>');
                }
            });

            function updateProductOptions() {
                let selectedProducts = [];

                // Step 1: Gather all selected products from all productSelects
                $('.productSelect').each(function () {
                    let values = $(this).val() || [];
                    selectedProducts = selectedProducts.concat(values);
                });

                // Step 2: Update all selects
                $('.productSelect').each(function () {
                    let currentSelect = $(this);
                    let currentValues = currentSelect.val() || [];

                    currentSelect.find('option').each(function () {
                        let optionVal = $(this).val();

                        if (optionVal === "") return; // skip placeholder

                        // Disable if selected in other rows (not in this row)
                        if (selectedProducts.includes(optionVal) && !currentValues.includes(optionVal)) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });

                    // Force Select2 to refresh its view of the options
                    currentSelect.select2({
                        placeholder: "Select Product",
                        width: 'style'
                    });
                });
            }

            // Bind after select2 is initialized
            let isValidating = false;

            $(document).on('change', '.productSelect', async function () {
                if (isValidating) return; // re-trigger রোধ করো

                const productId = $(this).val();
                const vendorId = $(this).closest('tr').data('vendor-id');
                const $validationMsg = $(this).siblings('.product-validation-msg');
                const $currentSelect = $(this);

                $validationMsg.text('');

                if (!productId || productId.length === 0 || !vendorId) {
                    updateProductOptions();
                    return;
                }

                isValidating = true;

                try {
                    const isValid = await validateProduct(vendorId, productId);

                    if (!isValid) {
                        $validationMsg.text('This product is not assigned to this vendor.');
                        $currentSelect.val(null).trigger('change.select2'); // শুধু select2 UI update
                    } else {
                        $validationMsg.text('');
                    }
                } catch (error) {
                    console.error('Validation error:', error);
                    // নীরবে fail করো, message না দেখাও
                } finally {
                    isValidating = false;
                    updateProductOptions();
                }
            });

            const validateProduct = async (vendorId, productId) => {
                try {
                    const url = "{{ route('requisition.vendor.product.validation', ['vendor_id' => ':vendorId', 'product_id' => ':productId']) }}"
                        .replace(':vendorId', vendorId)
                        .replace(':productId', productId);

                    console.log('Requesting:', url); // debug — remove after confirmed working

                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    return data.success === true;

                } catch (error) {
                    console.error('Error during product validation:', error);
                    throw error;
                }
            };
        });
    </script>

@endpush
