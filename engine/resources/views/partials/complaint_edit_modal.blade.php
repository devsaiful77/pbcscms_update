@php
    use App\SubgroupInfo;
    use App\GroupInfo;
@endphp
@php
    use App\Setting;
    $settings = Setting::first();
    if (!empty($settings) && !empty($settings->file_size_limit)) {
        $fileSizeLimit = (int) $settings->file_size_limit / 1024;
    } else {
        $fileSizeLimit = 10240 / 1024;
    }
@endphp

<div class="modal fade modal-xl" id="complaintEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        @inject('queueDuration','App\Services\UtilService')
        <h5 class="modal-title">Complaint Detail <small style="color: black; font-weight: bold;">(Ticket No. : {{$dataForView['reference_number']}})</small></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="assignForm" action="{{ route('new.assing.submit', encrypt($dataForView['reference_number'])) }}" method="POST">
          @csrf
            <div class="modal-body">
                <fieldset class="scheduler-border" style="background-color:#ffff">
                    <div class="scheduler-border">
                        <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                            Information <i class="fa-plus fa" aria-hidden="false"></i>
                        </a>
                    </div>
                    <div class="table-responsive collapse in" id="collapseOne">
                    <table class="table table-condensed table-bordered no-padding-margin-b">
                        <tr>
                            <th class="vcenter text-left">Ticket No.</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['reference_number']) ? $dataForView['reference_number'] : "" }}</td>
                            <th class="vcenter text-left">Account/Card No.</th>
                            <td class="vcenter text-left">
                                {{ !empty($dataForView['account_number']) ? $dataForView['account_number'] . (!empty($acc_br_code) ? '\\' . $acc_br_code : '') : '' }}
                            </td>
                            <th class="vcenter text-left">Account Name</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['acc_name']) ? $dataForView['acc_name'] : "" }}</td>
                        </tr>
                        <tr>
                            <th class="vcenter text-left">Mobile No.</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['mobile_number']) ? $dataForView['mobile_number'] : "" }}</td>
                            <th class="vcenter text-left">Customer Email.</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['email_address']) ? $dataForView['email_address'] : "" }}</td>
                            <th class="vcenter text-left">{{ !empty($dataForView['customer_name']) ? "Customer Name" : "" }}</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['customer_name']) ? $dataForView['customer_name'] : "" }}</td>
                        </tr>
                        <tr>
                            <th class="vcenter text-left">Ticket Creation Time</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['time_and_ext']) ? $dataForView['time_and_ext'] : "" }}</td>
                            <th class="vcenter text-left">Customer ID</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['SIF_Number']) ? $dataForView['SIF_Number'] : "" }}</td>
                            <th class="vcenter text-left">Product Type.</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['product_name']) ?  $dataForView['product_name'] : $dataForView['product_type'] }}</td>
                        </tr>
                        @if($dataForView["product_type"] == 3)
                            <tr>
                                <th class="vcenter text-left">{{ !empty($dataForView['mask_card_no']) ? "Inputted Masked Card Number" : "" }}</th>
                                <td class="vcenter text-left">{{ !empty($dataForView['mask_card_no']) ? $dataForView['mask_card_no'] : "" }}</td>
                                <th class="vcenter text-left">{{ !empty($dataForView['product_desc']) ? "Product Name - Description" : "" }}</th>
                                <td class="vcenter text-left">{{ !empty($dataForView['product_desc']) ? $dataForView['product_desc'] : "" }}</td>
                                <th class="vcenter text-left">{{ !empty($dataForView['account_status']) ? "Account Status" : "" }}</th>
                                <td class="vcenter text-left">{{ !empty($dataForView['account_status']) ? $dataForView['account_status'] : "" }}</td>
                            </tr>
                            <tr>
                                <th class="vcenter text-left">{{ !empty($dataForView['acc_opening_branch']) ? "Account Opening Branch" : `&nbsp` }}</th>
                                <td class="vcenter text-left">{{ !empty($dataForView['acc_opening_branch']) ? $dataForView['acc_opening_branch'] : `&nbsp` }}</td>
                                <th class="vcenter text-left">&nbsp;</th>
                                <td class="vcenter text-left">&nbsp;</td>
                                <th class="vcenter text-left">&nbsp;</th>
                                <td class="vcenter text-left">&nbsp;</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="vcenter text-left">{{ !empty($dataForView['segment']) ? "Segment Code" : "" }}</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['segment']) ? $dataForView['segment'] : "" }}</td>
                            @if($dataForView["product_type"] == 1)
                            <th class="vcenter text-left">{{ !empty($dataForView['mask_card_no']) ? "Masked Card Number" : "" }}</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['mask_card_no']) ? $dataForView['mask_card_no'] : "" }}</td>
                            @else
                            <th></th>
                            <td></td>
                            @endif
                            <th class="vcenter text-left">{{ !empty($dataForView['date_of_birth']) ? "Customer DOB" : "" }}</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['date_of_birth']) ? $dataForView['date_of_birth'] : "" }}</td>
                        </tr>

                        <tr>

                            <th class="vcenter text-left">NID No.</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['customer_nid']) ? $dataForView['customer_nid'] : "" }}</td>
                            <th class="vcenter text-left">Passport No.</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['passpor_number']) ? $dataForView['passpor_number'] : "" }}</td>
                            <th class="vcenter text-left">Branch Code</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['branch_code']) ? $dataForView['branch_code'] : "" }}</td>
                        </tr>

                        <tr>
                            <th class="vcenter text-left">Customer Address</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['communication']) ? $dataForView['communication'] : "" }}</td>
                            <th class="vcenter text-left">Branch Name</th>
                            <td class="vcenter text-left">{{ !empty($dataForView['acc_opening_branch']) ? $dataForView['acc_opening_branch'] : "" }}</td>
                            <td class="vcenter text-left">
                                @if(!empty($dataForView['access_by']))
                                    @php
                                        $customer_id = !empty($dataForView['SIF_Number']) ? e($dataForView['SIF_Number']) : '';
                                        $accountNumber = !empty($dataForView['account_number']) ? e($dataForView['account_number']) : '';
                                        $subgroup_info_id = Auth::user()->user_unit->subgroup_info_id;

                                        $isTouchPoint = false;
                                        if ($subgroup_info_id) {
                                            $subgroup = SubgroupInfo::find($subgroup_info_id);
                                            if ($subgroup) {
                                                $group = GroupInfo::find($subgroup->group_info_id);
                                                if ($group && $group->group_level_id == 1) {
                                                    $isTouchPoint = true;
                                                }
                                            }
                                        }
                                    @endphp
                                    <!-- Button for balance inquiry -->
                                    @if($dataForView['product_name'] == "Accounts" && !$isTouchPoint)
                                        <button type="button" class="btn btn-warning form-group m-0 p-0 ballanceInquery"
                                                value="{{ $accountNumber }}, {{ $customer_id }}"
                                                onclick="overlay('show');">
                                            Balance Inquiry
                                        </button>
                                    @endif
                                @endif
                            </td>

                        </tr>
                    </table>
                    </div>
                </fieldset>

                <fieldset class="scheduler-border" style="background-color:#ffff">
                    <div class="scheduler-border">
                        <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseTow" aria-expanded="false" aria-controls="collapseTow" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                            Action <i class="fa fa-minus" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="table-responsive collapse show" id="collapseTow">
                        <table class="table table-condensed table-bordered no-padding-margin-b">
                            <tr>
                                <th class="vcenter text-left">Priority.</th>
                                <td class="vcenter text-left">{{$dataForView['priority']}}</td>
                                <th class="vcenter text-left">Source.</th>
                                <td class="vcenter text-left">{{$dataForView['source']}}</td>
                                <th class="vcenter text-left">Tin Verified.</th>
                                <td class="vcenter text-left">{{$dataForView['tin_verified']}}</td>

                            </tr>
                            <tr>
                                <th class="vcenter text-left">Repeat Complaint.</th>
                                <td class="vcenter text-left">{{$dataForView['repeat_complaint']}}</td>
                                {{--<th class="vcenter text-left">Amount.</th>
                                <td class="vcenter text-left">{{$dataForView['amount']}}</td>--}}
                                <th class="vcenter text-left">Caller ID.</th>
                                <td class="vcenter text-left">{{$dataForView['caller_id']}}</td>
                                <th></th>
                                <td></td>
                            </tr>
                            <tr>

                                <th class="vcenter text-left">Complaint Type.</th>
                                <td class="vcenter text-left">{{$dataForView['issue_name']}}</td>
                                <th class="vcenter text-left">Complaint Detail.</th>
                                <td class="vcenter text-left" colspan="3">{{$dataForView['complaint_details']}}</td>
                            </tr>

                            @include('Supports/complaint_details_extended')

                            <tr>
                                <th colspan="6">&nbsp;</th>
                            </tr>

                            <tr class="success">
                                <th class="vcenter text-left">Form Logger.</th>
                                <td class="vcenter text-left">{{$dataForView['created_by']}}</td>
                                <th class="vcenter text-left">Time.</th>
                                <td class="vcenter text-left">{{date('d-m-Y h:i:s A', $dataForView['date'])}}</td>
                                <th class="vcenter text-left">&nbsp;</th>
                                <td class="vcenter text-left">&nbsp;</td>
                            </tr>
                            <tr class="success">
                                <th class="vcenter text-left">Current Position</th>
                                <td class="vcenter text-left">
                                    @if($dataForView['form_status'] == 11)
                                        Form Close at {{ $dataForView['name'] }} [{{ $dataForView['unit_id'] <> 2 ? "Maker" : "Checker" }}
                                    @else
                                        {{ $dataForView['name'] }} [{{ $dataForView['unit_id'] <> 2 ? "Maker" : "Checker" }}]
                                    @endif
                                </td>
                                <th class="vcenter text-left">Last Access By.</th>
                                <td class="vcenter text-left">
                                    @IF(!empty($dataForView['access_by']))
                                        {{$dataForView['access_by']}}
                                    @ELSE
                                        <?php
                    $lastAccess = "";
                    if (!empty($dataForView['comment']))
                    {
                    $lastAccess = end($dataForView['comment']) ['user_id'];
                    }
                    ?>
                                        @IF(!empty($lastAccess))
                                            {{$lastAccess}}
                                        @ELSE
                                            {{$dataForView['created_by']}}
                                        @ENDIF
                                    @ENDIF
                                </td>
                                <th class="vcenter text-left">&nbsp;</th>
                                <td class="vcenter text-left">&nbsp;</td>
                            </tr>

                        </table>
                    </div>
                </fieldset>

                <fieldset class="scheduler-border" style="background-color:#ffff">
                    <div class="scheduler-border">
                        <a class="colla" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="cursor: pointer; font-weight: bold; color:#ffffff;">
                            Ticket History <i class="fa-plus fa" aria-hidden="false"></i>
                        </a>
                    </div>
                    <div class="table-responsive collapse" id="collapseThree">
                        <table class="table table-condensed table-bordered no-padding-margin-b">
                            <?php
                                $sqlFormStatus = \Illuminate\Support\Facades\DB::table('comments')->select(
                                // 'comments.*',
                                'comments.user_id', 'comments.reference_number', 'comments.time', 'comments.group_id', 'comments.subgroup_id', 'comments.action', 'comments.comments', 'comments.isapproved', 'comments.time', 'users.name',)
                                ->join('users', 'comments.user_id', '=', 'users.user_id')
                                ->where('comments.reference_number', $dataForView['reference_number'])->orderBy('comments.time', 'ASC')
                                ->get();

                                if (count($sqlFormStatus) > 0)
                                {
                            ?>
                            <br/>
                            <table style="width: 100%; margin: 0 auto; border-spacing: 2px; border-collapse: separate;" border="0" >
                                <tr>
                                    <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border:1px solid #9acd32; font-weight: bold; font-family: serif;padding-left: 5px;">Person</td>
                                    <td class="topandbottom" style="border:1px solid #9acd32; font-weight: bold; font-family: serif;padding-left: 5px;">Log / In Time</td>
                                    <td class="topandbottom" style="border:1px solid #9acd32; font-weight: bold; font-family: serif;padding-left: 5px;">Task Touch Time</td>
                                    <td class="topandbottom" style="border:1px solid #9acd32; font-weight: bold; font-family: serif;padding-left: 5px;">Status</td>
                                    <td class="topandbottom" style="border:1px solid #9acd32; font-weight: bold;font-family: serif;padding-left: 5px;">Close / Out Time</td>
                                    <td class="topandbottom text-center" style="border:1px solid #9acd32; font-weight: bold;font-family: serif;padding-left: 5px;">Duration (D:H:M:S)</td>
                                    <td class="topandbottom" style="border:1px solid #9acd32; font-weight: bold;font-family: serif;padding-left: 5px;">Remarks</td>
                                </tr>
                                <?php
                                    $duration_in_minutes = 0;
                                    try
                                    {
                                        if (!empty($_GET['qd']))
                                        {
                                            $duration_in_minutes = decrypt($_GET['qd']);
                                        }
                                    }
                                    catch(DecryptException $e)
                                    {

                                    }

                                    $i = 0;
                                    $j = 0;
                                    $models = array();
                                    $prevgID = '';
                                    $lastInTime = "";
                                    //echo count($sqlFormStatus);
                                    foreach ($sqlFormStatus as $row)
                                    {

                                        $groupID = $row->group_id;
                                        $subGroupID = $row->subgroup_id;
                                        $userID = $row->user_id;
                                        $form_status = $row->action;
                                        $comments = $row->comments;
                                        $isapproved = $row->isapproved;
                                        $userName = $row->name;
                                        $models[$i]['group_id'] = $groupID;
                                        $models[$i]['duration_in_minutes'] = "";

                                        if ($prevgID == $userID)
                                        {
                                            $models[$i]['user_id'] = '';
                                            $models[$i]['user_name'] = '';
                                        }
                                        else
                                        {
                                            $models[$i]['user_id'] = $userID;
                                            $models[$i]['user_name'] = $userName;
                                        }

                                        if ($i == 0)
                                        {
                                            $models[$i]['isapproved'] = 1;
                                            $models[$i]['in_time'] = $row->time;
                                            $models[$i]['work_time'] = $row->time;
                                            $models[$i]['out_time'] = $row->time;
                                            $lastInTime = $row->time;
                                            $models[$i]['duration_in_minutes'] = $queueDuration->queueDurationCalculator(date('Y-m-d H:i:s', $lastInTime) , date('Y-m-d H:i:s', $row->time));
                                        }
                                        elseif ($prevgID != $userID)
                                        {
                                            $models[$i]['in_time'] = (!empty($models[$i - 1]['out_time'])) ? $models[$i - 1]['out_time'] : $row->time;
                                            $models[$i]['work_time'] = $row->time;
                                            $models[$i]['out_time'] = 0;
                                            $lastInTime = (!empty($models[$i - 1]['out_time'])) ? $models[$i - 1]['out_time'] : $row->time;
                                        }
                                        elseif ($prevgID == $userID && $i > 0 && $isapproved == 0)
                                        {
                                            $models[$i]['in_time'] = 0;
                                            $models[$i]['work_time'] = $row->time;
                                            $models[$i]['out_time'] = 0;
                                        }
                                        elseif ($prevgID == $userID && $i > 0 && $isapproved == 1)
                                        {
                                            $models[$i]['isapproved'] = $isapproved;
                                            $models[$i]['in_time'] = 0;
                                            $models[$i]['work_time'] = $row->time;
                                            $models[$i]['out_time'] = $row->time;
                                            $models[$i]['duration_in_minutes'] = $queueDuration->queueDurationCalculator(date('Y-m-d H:i:s', $lastInTime) , date('Y-m-d H:i:s', $row->time));
                                        }

                                        if (count($sqlFormStatus) == $i + 1)
                                        {
                                            $models[$i]['duration_in_minutes'] = $queueDuration->queueDurationCalculator(date('Y-m-d H:i:s', $lastInTime) , date('Y-m-d H:i:s'));
                                        }

                                        $models[$i]['form_status'] = $form_status;
                                        $models[$i]['comments'] = $comments;

                                        $prevgID = $userID;
                                        $i++;
                                    }

                                    foreach ($models as $key => $rowFormVal)
                                    {

                                        //pr($rowFormVal);
                                        $resultComm = '';
                                        $resultGName = $resultComm;

                                    ?>

                                    <tr>
                                        <?php /*if($rowFormVal['user_name'] != "") {*/ ?><!--
                                        <td class="topandbottom" style="border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;"><?php /*echo ''; */ ?></td>
                                        <?php /*}else{*/ ?>
                                        <td class="topandbottom" style="border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">&nbsp;</td>
                                        --><?php /*}*/ ?>
                                        <td class="topandbottom" style="border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">@if(!empty($rowFormVal['user_name'])){{ $rowFormVal['user_name'] }}@endif</td>
                                        <td class="topandbottom" style="border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">@if(!empty($rowFormVal['in_time']))<?php if (!empty($rowFormVal['in_time'] > 0)) echo date("d.m.Y ## h:i a", $rowFormVal['in_time']); ?>@endif</td>
                                        <td class="topandbottom" style="padding-top:5px;padding-bottom:5px; border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">@if(!empty($rowFormVal['work_time']))<?php if (!empty($rowFormVal['work_time'] > 0)) echo date("d.m.Y ## h:i a", $rowFormVal['work_time']); ?>@endif</td>
                                        <td class="topandbottom" style="border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">@if(!empty($rowFormVal['form_status']))<?php echo $rowFormVal['form_status']; ?>@endif</td>
                                        <td class="topandbottom" style="border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">@if(!empty($rowFormVal['out_time']))<?php if (!empty($rowFormVal['out_time'] > 0)) echo date("d.m.Y ## h:i a", $rowFormVal['out_time']); ?>@endif</td>
                                        <td class="" style="border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px; text-align:center"><?php echo $rowFormVal['duration_in_minutes'] ?></td>
                                        <td class="topandbottom" colspan="2" style="border:1px solid #9acd32; font-family: serif; color: #0072BB;padding-left: 5px;">{!! function_exists('render_ticket_comment') ? render_ticket_comment($rowFormVal['comments'] ?? '') : e($rowFormVal['comments'] ?? '') !!} &nbsp;</td>
                                    </tr>


                                    <?php
                                } ?>
                            </table>
                            <?php
                            } ?>
                            <table>
                                @IF(!empty($dataForView['complaint_attachment']))
                                    <tr><th colspan="6" class="vcenter text-left">Attachment</th></tr>
                                    @FOREACH($dataForView['complaint_attachment'] AS $attch)
                                        <tr>
                                            <td class="vcenter float-left">{{ $attch['attachment_date'] }}</td>
                                            <td class="vcenter float-left" colspan="5">&nbsp;
                                                <?php //echo $attch['file_name'];die;
                                                ?>
                                                @IF(!empty($attch['file_name']))
                                                    <?php
                                                        $basePath = str_replace('engine', '', base_path());
                                                        $imageURL = $basePath . 'public/attachments/' . $attch['file_name'];
                                                        // prd($imageURL);
                                                        if (file_exists($imageURL))
                                                        {
                                                    ?>
                                                    <a href="{{URL::asset('public/attachments/'.$attch['file_name'])}}" target="_blank">{{$attch['file_name']}}</a>
                                                    <a href="{{ route('download', ['filename' => $attch['file_name']]) }}"><i class="fa fa-download"></i></a>
                                                    @IF(Auth::user()->id == $attch['uploaded_by'])
                                                        <button class="attachmentdel btn btn-link error text-right no-padding-margin" data-filename="{{$attch['file_name']}}" data-id="{{$attch['id']}}"><i class="fa fa-trash"></i> </button>
                                                    @ENDIF
                                                    |
                                                    <?php
                                                        }
                                                        else
                                                    { ?>
                                                    <a href="{{ url('/images') }}" target="_blank">{{$attch['file_name']}}</a>
                                                    <a href="{{ route('download', ['filename' => $attch['file_name']]) }}"><i class="fa fa-download"></i></a>
                                                    <?php
                                                        }
                                                    ?>
                                                @ELSE
                                                    <strong style="color:red;">{{ "Attachment not available" }}</strong>
                                                @ENDIF
                                            </td>
                                        </tr>
                                    @ENDFOREACH
                                @ENDIF

                                @inject('flow_type','App\Services\WorkFlowService')

                                <?php
                                    $flow_type_name = $flow_type->getFlowTypeCheck($dataForView['reference_number']);

                                    $redirect_url = Request::url();
                                    $redirect_url .= (!empty($_GET)) ? '?' . http_build_query($_GET) : "";

                                    $editPermission = true;
                                    if (Auth::user()->can(['supportExecutive']))
                                    {

                                    if (!empty($dataForView['access_by']) && (Auth::user()->user_id != $dataForView['access_by']))
                                    {

                                        $editPermission = false;
                                    }
                                    if ($loggerCanAssign == false && $isAdminOrLogger == true)
                                    {
                                        $editPermission = false;
                                    }
                                    if (empty($_GET['qd']))
                                    {
                                        $editPermission = false;
                                    }
                                    }
                                    else if (Auth::user()->hasRole(['supervisor', 'srExecutive']))
                                    {
                                    $editPermission = false;
                                    }
                                    else //if(Auth::user()->hasRole(['logger', 'executive']))

                                    {
                                    //$editPermission = true;
                                    $editPermission = false;
                                    }
                                ?>

                                @if($flow_type_name==\App\Enum\FlowEnum::REGULAR)
                                    @inject('workflow_list','App\Services\WorkFlowService')
                                    @php $work = $workflow_list->workflowStage($dataForView['reference_number']);

                                                $sla_user=false;
                                                if(\Illuminate\Support\Facades\Auth::user()->hasRole([\App\Enum\RoleEnum::LOGGER,\App\Enum\RoleEnum::EXECUTIVE])){
                                                    $sla_user=true;
                                                }
                                                $touch = $work['touch'];
                                                $sla = $work['sla'];
                                                $hold = $work['hold'];
                                                $attach = $work['attach'];
                                                $attach_item = $work['attach_item'];
                                    @endphp
                                @else
                                    @php
                                        $sla_user=false;
                                        $touch='';
                                        $sla ='';
                                            $hold ='';
                                            $attach='';
                                        $attach_item ='';
                                    @endphp
                                @endif
                                @IF(((Auth::user()->hasRole(['superadmin', 'admin', 'logger'])) || (Auth::user()->can(['supportExecutive']) )) && $editPermission == true && $dataForView['form_status'] != 11 && (!empty($dataForView['access_by'])))
                                    @if($flow_type_name==\App\Enum\FlowEnum::REGULAR)
                                        @if($attach==1)
                                            @inject('attachmentCount','App\Services\UtilService')
                                            @php $attachmentItemCount = $attachmentCount->attachmentCount($dataForView['reference_number']); @endphp
                                            @if($attachmentItemCount < $attach_item)
                                                {!! Form::open(['method'=>'post', 'action' => ['SupportsController@uploadNewAttachment'] , 'enctype' => 'multipart/form-data']); !!}
                                                    {!! Form::token(); !!}

                                                    <tr><th colspan="6" class="">Attach New File<small class="error-message"> (Max file size is {{ $fileSizeLimit }} MB)  </small></th></tr>
                                                    <tr>
                                                        <td class="" colspan="6">
                                                            {{ Form::hidden('reference_number',encrypt($dataForView['reference_number'])) }}

                                                            {{ Form::hidden('redirect_url',$redirect_url) }}
                                                            <div class="form-inline">
                                                                <div class="custom-file">
                                                                    @for($i=0;$i < $attach_item-$attachmentItemCount; $i++)

                                                                    {!! Form::file('file_name[]', $attributes = array('class'=>'form-control', 'label'=>false, 'type'=>'file')); !!}
                                                                    @endfor
                                                                    <button type="submit" class="btn btn-success ml-1"><i class="fa fa-upload"></i> Upload</button>
                                                                </div>
                                                            @if($errors->has('file_name.*'))
                                                                <div class="error">
                                                                {!! implode('', $errors->all(':message')); !!}
                                                                </div>
                                                            @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                            {!! Form::close(); !!}
                                        @endif
                                    @endif
                                @else
                                    {!! Form::open(['method'=>'post', 'action' => ['SupportsController@uploadNewAttachment'] , 'enctype' => 'multipart/form-data']); !!}
                                        {!! Form::token(); !!}

                                        <tr><th colspan="6" class="">Attach New File<small class="error-message"> (Max file size is {{ $fileSizeLimit }} MB)  </small></th></tr>
                                        <tr>
                                            <td class="" colspan="6">
                                                {{ Form::hidden('reference_number',encrypt($dataForView['reference_number'])) }}

                                                {{ Form::hidden('redirect_url',$redirect_url) }}
                                                <div class="form-inline">
                                                    <div class="custom-file">
                                                    {!! Form::file('file_name[]', $attributes = array('class'=>'form-control', 'label'=>false, 'type'=>'file','multiple')); !!}
                                                        <button type="submit" class="btn btn-success ml-1"><i class="fa fa-upload"></i> Upload</button>
                                                    </div>
                                                @if($errors->has('file_name.*'))
                                                    <div class="error">
                                                    {!! implode('', $errors->all(':message')); !!}
                                                    </div>
                                                @endif
                                                </div>
                                            </td>
                                        </tr>
                                    {!! Form::close(); !!}
                                @endif
                                @ENDIF
                            </table>
                        </table>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3 text-start">
                            <input type="hidden" name="qd" value="{{ $qd }}">
                            <label for="floatingSelect" class="form-label text-black fw-bold">
                                Assign To <span class="text-danger">*</span>
                            </label>

                            <select class="form-select w-50" name="user_id" id="floatingSelect" data-placeholder="Select Users" required>
                                @if(!empty($userData))
                                    <option value="">Please Select User</option>
                                    @foreach($userData as $data)
                                        <option value="{{ $data['user_id'] }}">
                                            {{ $data['name'] }} [ {{ $data['user_id'] }} ]
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                            <div class="error text-danger mt-1">{{ $errors->first('user_id') }}</div>
                            <span class="text-danger" id="user_id_error"></span>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </div>
      </form>
    </div>
  </div>
</div>
<script>
    $(document).on('submit', '#assignForm', function(e) {
        var userId = $(this).find('[name="user_id"]').val();
        if (!userId) {
            e.preventDefault();
            $('#user_id_error').text('Please select user!');
        }
    });
</script>

