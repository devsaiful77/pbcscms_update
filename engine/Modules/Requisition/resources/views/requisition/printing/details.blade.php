@php
    use Modules\Requisition\App\Constants\RequisitionConstants;
    use App\Services\UtilService;
@endphp
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
                            <h2 class="p-2">Printing Stationary - Requisition Details</h2>
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
    <form action="{{ route('requisition.workingOnPrintREQ') }}" method="POST" class="mb-2">
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
                            <th width="25%">Product List</th>
                            <th width="10%">Unit Price</th>
                            @hasanyrole('requisitionAdminMaker|requisitionAdminChecker|requisitionSupervisor')
                            <th width="15%">Stock Qty</th>
                            @endhasrole
                            {{-- @hasrole('requisitionAdminMaker')
                            <th width="15%">Remaining  Qty</th>
                            @endhasrole --}}
                            <th width="15%">Request Qty</th>
                            <th width="15%">Approve Qty</th>
                        </tr>
                  </thead>
                  <tbody class="table-group-divider">
                  @foreach($requisition->printings as $key => $item)
                    <input type="hidden" name="stock_qty[{{ $item->id }}]" value="{{ $item?->requItem?->stock?->quantity ?? 0 }}">
                    <input type="hidden" name="product_id[{{ $item->id }}]" value="{{ $item?->requItem?->id }}">
                      <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ $item?->requItem?->product_name }}</td>
                          <td>{{ $item?->requItem?->unit_price ?? 'N/A' }}</td>
                          @hasanyrole('requisitionAdminMaker|requisitionAdminChecker|requisitionSupervisor')
                          <td>{{ $item?->requItem?->stock?->quantity ?? 0 }}</td>
                          @endhasrole
                          {{-- @hasrole('requisitionAdminMaker')
                          <td>{{ $item?->requItem?->stock?->quantity - $item->quantity }}</td>
                          @endhasrole --}}
                          <td>{{ $item->quantity }}</td>
                           <td>
                                @php
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

                            @foreach($requisition->printingRequhistories as $comment)
                            <tr>
                                <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border-bottom:1px solid #9acd32; border-left:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">{{ $comment->user_id }} [{{ $comment?->user?->name }}]</td>
                                <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border-bottom:1px solid #9acd32; border-left:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">{{ $comment->action }}</td>
                                <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border-bottom:1px solid #9acd32; border-left:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">{{ date("d.m.Y ## h:i a", (int)$comment->time)}}</td>
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
                                <th width="50%">Title</th>
                                <th width="50%">Attachment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->requAttachments as $attach)
                                @if ($attach->is_admin_attach == 1)
                                    <tr>
                                        <td><span>{{ $attach->attachment_title }} </span></td>
                                        <td><a href="{{URL::asset('public/attachments/'.$attach->attachment)}}" target="_blank">{{$attach->attachment}}</a></td>
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
                        <button type="submit" name="action" value="close" onclick="overlay('show');" class="btn btn-danger mt-2">
                            <i class="fa fa-lock"></i> Close This Requisition</button>
                    </div>
                @endif
            @endif
            @if($actionAccess)
                @if(empty($requisition->access_by))
                    @if(in_array($requisition->unit_id,userUnits())  || $isSupervisor)
                        <a href="{{ route('requisition.printing.assign', encrypt($requisition->requisition_number)) }}" class="btn btn-primary"  onclick="overlay('show');">Assign</a>
                    @endif
                @else
                    @if(Auth::user()->user_id == $requisition->access_by && ($requisition->status != RequisitionConstants::REQ_CLOSE && $requisition->status != RequisitionConstants::REQ_CANCEL))
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
                                        <!-- Choose Item -->
                                        <div class="col-md-4">
                                            <label class="form-label">Choose Item</label>
                                            <select class="form-select" name="item_id">
                                                <option value="">Select Item</option>
                                                @foreach($requisition->printings as $key => $item)
                                                    <option value="{{ $item?->requItem?->id }}">{{ $item?->requItem?->product_name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger" id="item_error"></span>
                                        </div>

                                        <!-- Attachment Title -->
                                        <div class="col-md-4">
                                            <label class="form-label">Attachment Title</label>
                                            <input type="text" name="attachment_title" class="form-control" placeholder="Attachment Title">
                                            <span class="text-danger" id="title_error"></span>
                                        </div>

                                        <!-- Attachment File -->
                                        <div class="col-md-4">
                                            <label class="form-label">Attachment</label>
                                            <input type="file" class="form-control" name="attachment" id="attachmentInput">
                                            <span class="text-danger" id="attachment_error"></span>
                                        </div>

                                        <!-- Upload Button -->
                                        <div class="col-12 mb-4">
                                            <button type="button" class="btn btn-success" id="uploadBtn">Upload</button>
                                        </div>

                                    </div>
                                </div>
                            </fieldset>
                        @endhasanyrole
                        <button type="button" class="btn btn-info btn-sm float-end" onclick="printByClass('print-area')"><i class="fa fa-print"></i> Print This Page</button>
                        <div class="mb-3 col-5">
                            <label for="comments" class="form-label">Comment</label>
                            <textarea name="comments" id="comments" rows="3" class="form-control" placeholder="Enter your comment here..." ></textarea>
                            @IF($errors->has('comments'))
                                <div class="error-message">{{ $errors->first('comments') }}</div>
                            @ENDIF
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
        $(document).ready(function () {
            $('#uploadBtn').on('click', function (e) {
                // Prevent outer form submission
                e.preventDefault();

                $('#title_error').text('');
                $('#attachment_error').text('');
                $('#item_error').text('');

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
        });
    </script>

@endpush
