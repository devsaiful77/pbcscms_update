@extends('layouts.admin')
@section('content')
<script src="{{ URL::asset('public/js/latest-v/flatpickr-4.6.13.min.js') }}"></script>

{!! Form::open(['method'=>'post', 'action' => ['SupportsController@submitComplaint'] , 'enctype' => 'multipart/form-data', 'id' => 'mainForm']); !!}
    {!! Form::token(); !!}

  <style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance:textfield;
    }
    #issue_extra .none-bottom-border,
    #issue_check_list .none-bottom-border{
        border-bottom-width: 0;
        padding: 0;
    }
    #issue_extra .none-bottom-border .table-condensed,
    #issue_check_list .none-bottom-border .table-condensed{
        margin: 0;
    }
    .navbar{
        display: none;
    }
  </style>

	<div class="curved-inner-pro py-2">
        <div class="curved-ctn">
            <h2>New Complaint Form</h2>
        </div>
    </div>
    @php
        use App\Setting;
        $settings = Setting::first();
        if (!empty($settings) && !empty($settings->file_size_limit)) {
            $fileSizeLimit = (int) $settings->file_size_limit / 1024;
        } else {
            $fileSizeLimit = 10240 / 1024;
        }
    @endphp
    @if(session('error'))
        <div class="alert alert-danger alert-block">
            <strong>{{ session('error') }}</strong>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-block" style="font-size: 14px; margin-bottom: 20px; border-left: 5px solid #d9534f;">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong><i class="fa fa-exclamation-triangle"></i> Please correct the validation errors below and submit.:</strong>
            <ul style="margin-top: 8px; margin-bottom: 0; padding-left: 25px;">
                @foreach ($errors->all() as $error)
                    <li style="margin-bottom: 3px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<div class="pb-5">
	<div class="row">
        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 col-xs-8">
            <div class="table-responsive">
                <fieldset class="scheduler-border" style="background-color:#f5f5f5">
                    <div class="scheduler-border">Action Fields</div>
                    <div class="table-responsive">
                    <table class="table table-condensed">
                    <colgroup>
                        <col width="15%"></col>
                        <col width="35%"></col>
                        <col width="15%"></col>
                        <col width="35%"></col>
                    </colgroup>

                    <tr>
                      <th class="vcenter">Priority</th>
                      <td class="vcenter">
                        {{ Form::select('priority', [null=>'Select Priority'] +  unserialize(PRIORITY), (!empty($dataForView['priority'])) ? $dataForView['priority'] : "", ['class'=>'form-control']) }}
                      </td>
                      <th class="vcenter">Source</th>
                      <td class="vcenter">
                        {{ Form::select('source', [null=>'Select Source'] +  $allSourceData, (!empty($dataForView['source'])) ? $dataForView['source'] : "", ['class'=>'form-control']) }}
                      </td>

                    </tr> <!-- Priority and Time & Ext -->

                     <!-- Complaint Detail and Caller ID -->
                     <!-- Source & Repeat Complaint -->
                    <tr>
                      <th class="vcenter">Tin Verified</th>
                      <td class="vcenter">
                        {{ Form::select('tin_verified', [null=>'Please Select'] +  unserialize(CONFIRMATION), (!empty($dataForView['tin_verified'])) ? $dataForView['tin_verified'] : "", ['class'=>'form-control']) }}
                      </td>

                      <th class="vcenter">Repeat Complaint</th>
                      <td class="vcenter">
                        {{ Form::select('repeat_complaint', unserialize(CONFIRMATION), (!empty($dataForView['repeat_complaint'])) ? $dataForView['repeat_complaint'] : "No", ['class'=>'form-control']) }}
                      </td>

                      {{--<th class="vcenter">Amount</th>
                      <td class="vcenter">
                        {!! Form::text('amount',(!empty($dataForView["amount"])) ? $dataForView["amount"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Amount']); !!}
                        @IF($errors->has('amount')) <div class="error-message">{{ $errors->first('amount') }}</div> @ENDIF
                      </td>--}}

                    </tr> <!-- Tin Verified  & Amount -->

                    <tr>
                      <th class="vcenter">Complaint Detail<span class="required">*</span></th>
                      <td class="vcenter">
                        {!! Form::textarea('complaint_details',(!empty($dataForView["complaint_details"])) ? $dataForView["complaint_details"] : ''  ,['rows'=>2, 'class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'placeholder'=>'Complaint Detail']); !!}
                        @IF($errors->has('complaint_details')) <div class="error-message">{{ $errors->first('complaint_details') }}</div> @ENDIF
                      </td>

                      @php if(Auth::user()->user_unit->subgroup_info_id==3){ @endphp
                        <th class="vcenter">Caller ID<span class="required">*</span></th>
                      @php }else{ @endphp
                        <th class="vcenter">Caller ID</th>
                      @php } @endphp
                      <td class="vcenter">
                        {!! Form::number('caller_id',(!empty($dataForView["caller_id"])) ? $dataForView["caller_id"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Caller ID']); !!}
                        @IF($errors->has('caller_id')) <div class="error-message">{{ $errors->first('caller_id') }}</div> @ENDIF
                      </td>
                    </tr>

                    <tr style="background: #fcf8e3; border-top: 2px solid #f0ad4e;">
                        <td colspan="4" style="padding: 7px 12px; font-weight: bold; color: #8a6d3b;">
                            <i class="fa fa-folder-open"></i> Complaint #1
                        </td>
                    </tr>
                    <tr>
                    <th class="vcenter">Complaint Category  <span class="required">*</span></th>
                      <td class="vcenter">

                        @inject('allComplaintCategory','App\Services\UtilService')
                        @php
                          $complaintCategoryResult = $allComplaintCategory->getComplaintCategory($dataForView["account_type"]);
                          $pBComplaintCat = old('complaint_category');
                        @endphp

                        <select class="form-control wFormType" name="complaint_category" id="complaint_category">
                          <option value="">Select Category</option>
                          @foreach($complaintCategoryResult as $allComCategory)
                            @php
                            $selectedComplaintCat = "";
                            if($pBComplaintCat == $allComCategory->id) {
                                $selectedComplaintCat = "selected";
                            }
                            @endphp
                            <option value="{{ $allComCategory->id }}" {{$selectedComplaintCat}}> {{ $allComCategory->name }} </option>
                          @endforeach
                        </select>

                        {{-- Form::select('complaint_category', [null=>'Select Complaint Category'], (!empty($dataForView['complaint_category'])) ? $dataForView['complaint_category'] : "", ['class'=>'form-control']) --}}
                      </td>

                    <th class="vcenter">Complaint Sub Category<span class="required">*</span></th>
                      <td class="vcenter">

                        @inject('allForms','App\Services\UtilService')
                        @php
                          $allIssue= $allForms->getAllComplaintWithProd($dataForView["account_type"]);
                        @endphp

                        {{--{{ Form::select('w_form_type', [null=>'Please Select'] +  $allUnitItemData, (!empty($dataForView['w_form_type'])) ? $dataForView['w_form_type'] : "", ['class'=>'form-control wFormType']) }}--}}

                        <select class="form-control wFormType" name="complaint_type" id="complaint_type">
                        <option value="">Select Complaint</option>

                        </select>
                        {{--{{ Form::select('complaint_type', [null=>'Please Select'] +  $allUnitItemData, (!empty($dataForView['complaint_type'])) ? $dataForView['complaint_type'] : "", ['class'=>'form-control wFormType','id'=>'complaint_type']) }}--}}
                        @IF($errors->has('complaint_type')) <div class="error-message">{{ $errors->first('complaint_type') }}</div> @ENDIF
                      </td>

                      </tr>

                      <tr id="issue_extra">
                          @php
                            $pbCompType="";
                            if(!empty(old('complaint_type'))) {
                              $service_request = $pbCompType = old('complaint_type');
                              /*$issue_fields = App\IssueConfig::where('issue_id',$service_request)->get();*/
                              $issue_fields = App\Services\FieldSetGroupService::getFieldSet($service_request);
                              $check_lists = App\IssueCheckListConfig::where('issue_id',$service_request)->get();
                            }
                          @endphp
                        @include('partials.extra_form_field_with_group')
                      </tr>

                    <tr id="issue_check_list">
                          @include('partials.issue_check_list')
                    </tr>
                    {{-- attachment items for Complaint #1 --}}
                    <tr id="attachment_items_wrapper">
                        <!-- <th>
                            <a href="#" data-id="" class="btn btn-warning" id="isInquiryApi" style="display: none;">Inquiry API</a>
                        </th>
                        <td></td> -->
                        <th class="vcenter">Attachments
                            <div class="clearfix"></div>
                            <small class="error-message">(Max file size is {{ $fileSizeLimit }} MB)</small></th>
                        <td class="vcenter" id="attachment_item">
                            @include('partials.maker_attachment_item')
                            <div class="clearfix pb-2"></div>
                            <img id="imgInComPage" alt="W-Form image" width="100" height="100" style="display:none;" />
                            <a id="downloadBtnComPage" href="#" style="display:none;" download>Download File</a>

                            {!! Form::file('file_name[0][]', $attributes = array(
                                'class' => 'form-control',
                                'label' => false,
                                'type' => 'file',
                                'multiple' => 'multiple',
                                'onchange' => "
                                    var file = this.files[0];
                                    var img = document.getElementById('imgInComPage');
                                    var downloadBtn = document.getElementById('downloadBtnComPage');
                                    if (file) {
                                        if (file.type.startsWith('image/')) {
                                            // Show image preview
                                            img.style.display = 'block';
                                            img.src = window.URL.createObjectURL(file);
                                            downloadBtn.style.display = 'none';
                                            downloadBtn.href = '';
                                        } else {
                                            // Hide image and show download button
                                            img.style.display = 'none';
                                            img.src = '';
                                            downloadBtn.style.display = 'block';
                                            downloadBtn.href = window.URL.createObjectURL(file);
                                            downloadBtn.innerHTML = 'Download ' + file.name;
                                        }
                                    } else {
                                        // Hide both if no file is selected
                                        img.style.display = 'none';
                                        img.src = '';
                                        downloadBtn.style.display = 'none';
                                        downloadBtn.href = '';
                                    }"
                            )) !!}

                        </td>
                        {{--<td class="vcenter" id="issue_attachment_item"></td>--}}
                    </tr>
                    @IF($errors->has('file_name.*') || $errors->has('file_name.0.*'))
                      <tr>
                        <th colspan="3"></th>
                        <th> <div class="error-message">{{ $errors->first('file_name.*') ?: $errors->first('file_name.0.*') }}</div> </th>
                      </tr>
                    @ENDIF
                    </tbody>
                    <tbody id="additional_complaints_container"></tbody>
                    <tbody>
                    <tr id="add_more_complaint_row">
                        <td colspan="4" style="background: #fdfaf6; padding: 10px 15px; border-top: 2px dashed #f0ad4e; border-bottom: 2px dashed #f0ad4e;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 600; color: #8a6d3b; font-size: 13px;">
                                    <i class="fa fa-info-circle"></i> If Customer has any further complaints, press the '+' button to add them.:
                                </span>
                                <button type="button" class="btn btn-sm btn-warning" id="btn_add_complaint_request" style="font-weight: 600; color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <i class="fa fa-plus-circle"></i> Add Another Complaint (+)
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>


                  </table>
                  </div>
                </fieldset>
            </div>
        </div>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="table-responsive">
                <fieldset class="scheduler-border" style="background-color:#f5f5f5">
                    <div class="scheduler-border">Customer's Information</div>
                    <div class="table-responsive">
                    <table class="table table-condensed" id="customer_info_table">
                        <colgroup>
                        <col width="40%"></col>
                        <col width="60%"></col>
                        </colgroup>

                        @if ($dataForView["account_type"] != 1 && $dataForView["account_type"] != 3)
                            <tr>
                                <th class="vcenter borderright">Account/ID No</th>
                                <td class="vcenter">
                                    {!! Form::text('account_number',(!empty($dataForView["account_number"])) ? $dataForView["account_number"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'autofocus'=>'true', 'placeholder'=>'Account/ID No', 'readonly']);!!}
                                    @IF($errors->has('account_number')) <div class="error-message">{{ $errors->first('account_number') }}</div> @ENDIF
                                </td>
                            </tr>
                            <tr>
                                <th class="vcenter borderright">Account Name</th>
                                <td class="vcenter">
                                    {!! Form::text('acc_name',(!empty($dataForView["acc_name"])) ? $dataForView["acc_name"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Account Name', 'readonly']); !!}
                                    @IF($errors->has('acc_name')) <div class="error-message">{{ $errors->first('acc_name') }}</div> @ENDIF
                                </td>

                            </tr><!-- Account/Card No & Customer Name -->
                            <tr>
                            <th class="vcenter borderright">Mobile Number</th>
                            <td class="vcenter">
                                {!! Form::text('mobile_number',(!empty($dataForView["mobile_number"])) ? $dataForView["mobile_number"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Mobile Number', 'readonly']); !!}
                                @IF($errors->has('mobile_number')) <div class="error-message">{{ $errors->first('mobile_number') }}</div> @ENDIF

                                {{-- Form::hidden('def_email_addr',(!empty($dataForView['def_email_addr'])) ? urldecode($dataForView['def_email_addr']) : '') --}}
                            </td>
                            </tr> <!-- Mobile Number & Email -->
                            <tr>
                            <th class="vcenter borderright">Customer Name</th>
                            <td class="vcenter">
                                {!! Form::text('customer_name',(!empty($dataForView["customer_name"])) ? $dataForView["customer_name"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Customer Name', 'readonly']); !!}
                                @IF($errors->has('customer_name')) <div class="error-message">{{ $errors->first('customer_name') }}</div> @ENDIF
                                {{--
                                <div class="form-control disabled" disabled>{{(!empty($dataForView['def_email_addr'])) ? urldecode($dataForView['def_email_addr']) : ''}}</div>
                                --}}
                            </td>
                            </tr> <!-- Mobile Number & Email -->



                            <tr>
                            <th class="vcenter borderright">Product Type<span class="required">*</span></th>
                            <td class="vcenter">
                                <select class="form-control" name="product_type" id="product_type">
                                @inject('product_type','App\Services\ProductTypeService')
                                {!! $product_type->getProductTypeByID(old($dataForView["account_type"],(!empty($dataForView["account_type"])) ? $dataForView["account_type"] : '')) !!}
                                @IF($errors->has('product_type')) <div class="error-message">{{ $errors->first('product_type') }}</div> @ENDIF
                            </td>

                            </tr> <!-- Product Type and Complaint Type -->
                            <tr>
                            <th class="vcenter borderright">Customer Email</th>
                            <td class="vcenter">
                                {!! Form::text('def_email_addr',(!empty($dataForView["def_email_addr"])) ? $dataForView["def_email_addr"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Customer Email', 'readonly']); !!}
                                @IF($errors->has('def_email_addr')) <div class="error-message">{{ $errors->first('def_email_addr') }}</div> @ENDIF
                            </td>

                            </tr> <!-- Product Type and Complaint Type -->

                            <tr>

                            <th class="vcenter borderright">Customer Number</th>
                                    <td class="vcenter">
                                        {!! Form::text('SIF_Number',(!empty($dataForView["CIF_number"])) ? $dataForView["CIF_number"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Customer Number', 'readonly']); !!}
                                        @IF($errors->has('SIF_Number')) <div class="error-message">{{ $errors->first('SIF_Number') }}</div> @ENDIF
                                    </td>


                            </tr>
                            <tr>

                            <th class="vcenter borderright">Time &amp; Ext<span class="required">*</span></th>
                                    <td class="vcenter">
                                        {!! Form::text('time_and_ext',(!empty($dataForView["time_and_ext"])) ? $dataForView["time_and_ext"] : date("h:i:s a") ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Time &amp; Ext*', 'readonly']); !!}
                                        @IF($errors->has('time_and_ext')) <div class="error-message">{{ $errors->first('time_and_ext') }}</div> @ENDIF
                                    </td>

                            </tr>

                        @endif

                        @if ($dataForView["account_type"] == 1 || $dataForView["account_type"] == 3)
                            <tr>
                                <th class="vcenter borderright">Card Number</th>
                                <td class="vcenter">
                                    {!! Form::text('account_number',(!empty($dataForView["account_number"])) ? $dataForView["account_number"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'autofocus'=>'true', 'placeholder'=>'Account/ID No', 'readonly']);!!}
                                    @IF($errors->has('account_number')) <div class="error-message">{{ $errors->first('account_number') }}</div> @ENDIF
                                </td>
                            </tr>
                          <tr>
                              <th class="vcenter borderright">Card Product Name</th>
                              <td class="vcenter">
                                  {!! Form::text('acc_name',(!empty($dataForView["cardProductName"])) ? $dataForView["cardProductName"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Account Name', 'readonly']); !!}
                                  @IF($errors->has('acc_name')) <div class="error-message">{{ $errors->first('acc_name') }}</div> @ENDIF
                              </td>

                          </tr><!-- Account/Card No & Customer Name -->
                          <tr>
                          <th class="vcenter borderright">Mobile Number</th>
                          <td class="vcenter">
                              {!! Form::text('mobile_number',(!empty($dataForView["mobile_number"])) ? $dataForView["mobile_number"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Mobile Number', 'readonly']); !!}
                              @IF($errors->has('mobile_number')) <div class="error-message">{{ $errors->first('mobile_number') }}</div> @ENDIF

                              {{-- Form::hidden('def_email_addr',(!empty($dataForView['def_email_addr'])) ? urldecode($dataForView['def_email_addr']) : '') --}}
                          </td>
                          </tr> <!-- Mobile Number & Email -->
                          <tr>
                          <th class="vcenter borderright">Customer Name</th>
                          <td class="vcenter">
                              {!! Form::text('customer_name',(!empty($dataForView["accountTitle"])) ? $dataForView["accountTitle"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Customer Name', 'readonly']); !!}
                              @IF($errors->has('customer_name')) <div class="error-message">{{ $errors->first('customer_name') }}</div> @ENDIF
                              {{--
                              <div class="form-control disabled" disabled>{{(!empty($dataForView['def_email_addr'])) ? urldecode($dataForView['def_email_addr']) : ''}}</div>
                              --}}
                          </td>
                          </tr> <!-- Mobile Number & Email -->



                          <tr>
                          <th class="vcenter borderright">Product Type<span class="required">*</span></th>
                          <td class="vcenter">
                              <select class="form-control" name="product_type" id="product_type">
                              @inject('product_type','App\Services\ProductTypeService')
                              {!! $product_type->getProductTypeByID(old($dataForView["account_type"],(!empty($dataForView["account_type"])) ? $dataForView["account_type"] : '')) !!}
                              @IF($errors->has('product_type')) <div class="error-message">{{ $errors->first('product_type') }}</div> @ENDIF
                          </td>

                          </tr> <!-- Product Type and Complaint Type -->
                          <tr>
                          <th class="vcenter borderright">Customer Email</th>
                          <td class="vcenter">
                              {!! Form::text('def_email_addr',(!empty($dataForView["email"])) ? $dataForView["email"] : '' ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Customer Email', 'readonly']); !!}
                              @IF($errors->has('def_email_addr')) <div class="error-message">{{ $errors->first('def_email_addr') }}</div> @ENDIF
                          </td>

                          </tr> <!-- Product Type and Complaint Type -->

                          <tr>

                          <th class="vcenter borderright">Client Code</th>
                                  <td class="vcenter">
                                      {!! Form::text('SIF_Number',(!empty($dataForView["clientCode"])) ? $dataForView["clientCode"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Customer Number', 'readonly']); !!}
                                      @IF($errors->has('SIF_Number')) <div class="error-message">{{ $errors->first('SIF_Number') }}</div> @ENDIF
                                  </td>


                          </tr>
                          <tr>

                          <th class="vcenter borderright">Time &amp; Ext<span class="required">*</span></th>
                                  <td class="vcenter">
                                      {!! Form::text('time_and_ext',(!empty($dataForView["time_and_ext"])) ? $dataForView["time_and_ext"] : date("h:i:s a") ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Time &amp; Ext*', 'readonly']); !!}
                                      @IF($errors->has('time_and_ext')) <div class="error-message">{{ $errors->first('time_and_ext') }}</div> @ENDIF
                                  </td>

                          </tr>
                        @endif

                        {{-- <tr>
                          <th class="vcenter borderright">Segment Code</th>
                                <td class="vcenter">
                                    {!! Form::text('segment',(!empty($dataForView["SegmentCode"])) ? $dataForView["SegmentCode"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Segment Code']); !!}
                                </td>

                        </tr> --}}

                        @if ($dataForView["account_type"] != 1 && $dataForView["account_type"] != 3)
                        <tr>
                                <th class="vcenter borderright">Customer DOB</th>
                                <td class="vcenter">
                                {!! Form::text('date_of_birth',(!empty($dataForView["date_of_birth"])) ? $dataForView["date_of_birth"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Customer DOB', 'readonly']); !!}

                                {{ Form::hidden('cb_fin_acctno', (!empty($dataForView["cb_fin_acctno"])) ? $dataForView["cb_fin_acctno"] : "") }}

                                {{ Form::hidden('card_status', (!empty($dataForView["card_status"])) ? $dataForView["card_status"] : "") }}

                                {{ Form::hidden('branch_code', (!empty($dataForView["branch_code"])) ? $dataForView["branch_code"] : "") }}
                                {{ Form::hidden('communication', (!empty($dataForView["communication"])) ? $dataForView["communication"] : "") }}
                                {{ Form::hidden('customer_nid', (!empty($dataForView["customer_nid"])) ? $dataForView["customer_nid"] : "") }}
                                {{ Form::hidden('passpor_number', (!empty($dataForView["passpor_number"])) ? $dataForView["passpor_number"] : "") }}
                                {{ Form::hidden('branchName', (!empty($dataForView["branchName"])) ? $dataForView["branchName"] : "") }}
                                </td>
                        </tr>
                        @endif

                        @if ($dataForView["account_type"] == 1 || $dataForView["account_type"] == 3)
                            {{--<tr>
                            <th class="vcenter borderright">Masked Card Number</th>
                            <td class="vcenter">
                                {!! Form::text('mask_card_no',(!empty($dataForView["mask_card_no"])) ? $dataForView["mask_card_no"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Masked Card Number']); !!}
                            </td>
                            </tr>--}}
                            <tr>
                                <th class="vcenter borderright">Card Status</th>
                                <td class="vcenter">
                                    {!! Form::text('',(!empty($dataForView["cardStatus"])) ? $dataForView["cardStatus"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Card Status', 'readonly']); !!}
                                </td>
                            </tr>

                            {{-- Hidden Fields with Table Row Structure --}}
                            <tr class="">
                                <th class="vcenter borderright">Customer DOB</th>
                                <td class="vcenter">
                                    {!! Form::text('date_of_birth',(!empty($dataForView["dob"])) ? $dataForView["dob"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Customer DOB', 'readonly']); !!}
                                </td>
                            </tr>

                            {{--<tr class="">
                                <th class="vcenter borderright">CB Fin Account No</th>
                                <td class="vcenter">
                                    {{ Form::text('cb_fin_acctno', (!empty($dataForView["cb_fin_acctno"])) ? $dataForView["cb_fin_acctno"] : "", ['class' => 'form-control']) }}
                                </td>
                            </tr>

                            <tr class="">
                                <th class="vcenter borderright">Card Status</th>
                                <td class="vcenter">
                                    {{ Form::text('card_status', (!empty($dataForView["card_status"])) ? $dataForView["card_status"] : "", ['class' => 'form-control']) }}
                                </td>
                            </tr>--}}

                            {{--<tr class="">
                                <th class="vcenter borderright">Branch Code</th>
                                <td class="vcenter">
                                    {{ Form::text('branch_code', (!empty($dataForView["branch_code"])) ? $dataForView["branch_code"] : "", ['class' => 'form-control']) }}
                                </td>
                            </tr>--}}

                            {{--<tr class="">
                                <th class="vcenter borderright">Communication</th>
                                <td class="vcenter">
                                    {{ Form::text('communication', (!empty($dataForView["communication"])) ? $dataForView["communication"] : "", ['class' => 'form-control']) }}
                                </td>
                            </tr>

                            <tr class="">
                                <th class="vcenter borderright">Customer NID</th>
                                <td class="vcenter">
                                    {{ Form::text('customer_nid', (!empty($dataForView["customer_nid"])) ? $dataForView["customer_nid"] : "", ['class' => 'form-control']) }}
                                </td>
                            </tr>

                            <tr class="">
                                <th class="vcenter borderright">Passport Number</th>
                                <td class="vcenter">
                                    {{ Form::text('passpor_number', (!empty($dataForView["passpor_number"])) ? $dataForView["passpor_number"] : "", ['class' => 'form-control']) }}
                                </td>
                            </tr>

                            <tr class="">
                                <th class="vcenter borderright">Branch Name</th>
                                <td class="vcenter">
                                    {{ Form::text('branchName', (!empty($dataForView["branchName"])) ? $dataForView["branchName"] : "", ['class' => 'form-control']) }}
                                </td>
                            </tr>--}}


                        @endif
                        {{--@if ($dataForView["account_type"] == 3)
                            <tr>
                                <th class="vcenter borderright">Account Status</th>
                                <td class="vcenter">
                                    {!! Form::text('account_status',(!empty($dataForView["account_status"])) ? $dataForView["account_status"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Account Status', 'readonly']); !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="vcenter borderright">Product Name - Description</th>
                                <td class="vcenter">
                                    {!! Form::text('product_desc',(!empty($dataForView["product_desc"])) ? $dataForView["product_desc"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Product Description', 'readonly']); !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="vcenter borderright">Inputted Masking Card No</th>
                                <td class="vcenter">
                                    {!! Form::text('inputted_masking_card',(!empty($dataForView["acc_number"])) ? $dataForView["acc_number"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Inputted Masking Card No', 'readonly']); !!}
                                </td>

                            </tr>
                            <tr>
                                <th class="vcenter borderright">Account Opening Branch</th>
                                <td class="vcenter">{!! Form::text('acc_opening_branch',(!empty($dataForView["acc_opening_branch"])) ? $dataForView["acc_opening_branch"] : "" ,['class' => 'form-control', 'label'=>false, 'autocomplete'=>'off', 'type'=>'text', 'placeholder'=>'Account Opening Branch', 'readonly']); !!}</td>
                            </tr>
                        @endif--}}
                    </table>
                    </div>
                </fieldset>
            </div>
        </div>



    </div>
        <div class="clearfix"></div>

    <?php
    $dataForView["active_tab"] = "complaint";
    $additionalParams = (!empty($dataForView)) ? '?'.http_build_query($dataForView) : "";
    ?>
    {{ Form::hidden('additionalParams',$additionalParams) }}
    <input type="hidden" name="multi_requests_data" id="multi_requests_data" value="{{ old('multi_requests_data') }}">

    {!!
      Form::submit((!empty($id)) ? 'Update':'Submit',array(
        'class'=>'btn btn-primary gradient',
        'title'=>'Add',
        'onclick'=>"overlay('show');",
        'escape'=>false
      ));
    !!}
    <?php $closeUrl = url('/Supports/home'); //$closeUrl = url('/Supports/home').$additionalParams ?>
    <!--
    <button type="button" class="btn btn-danger gradient" onclick="window.close();">Close</button> -->
    <a href="{{$closeUrl}}" class="btn btn-danger gradient">Close</a>
    <button class="btn btn-success printBtn" type="button">Print</button>
</div>

{!! Form::close(); !!}
<div class="clearfix">&nbsp;</div>
<div class="modal fade" id="inquiryModal" tabindex="-1" aria-labelledby="inquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inquiryModalLabel">Inquiry Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="inquiryData">
                    <div class="col-lg-6"></div>
                </div>
            </div>
        </div>
    </div>
</div>

  <script type="text/javascript">
    $(document).ready(function(){
      $(".printBtn").on('click', function(event) {
        event.preventDefault();

        // 1. Gather Customer Info
        var customerInfo = {};
        $('#customer_info_table :input').each(function () {
            var inputtype = $(this).prop('type');
            var notallowed = ['hidden', 'file', 'submit', 'button'];
            if ($.inArray(inputtype, notallowed) !== -1) return;

            var val = $(this).val();
            if (inputtype === 'select-one' || inputtype === 'select-multiple') {
                val = $(this).find('option:selected').text();
                if (val === 'Select' || val === 'Please Select') val = '';
            }

            var label = $(this).closest('td').prev('th').text();
            label = $.trim(label.replace(/\s+/g, ' ').replace(/\*/g, '').replace(/:$/, ''));
            if (label && val && $.trim(val) !== '' && val !== 'N/A') {
                customerInfo[label] = $.trim(val);
            }
        });

        // 2. Gather Action / General Info
        var generalInfo = {};
        var generalSelectors = [
            '[name="priority"]',
            '[name="source"]',
            '[name="tin_verified"]',
            '[name="repeat_complaint"]',
            '[name="caller_id"]'
        ];
        $.each(generalSelectors, function (i, sel) {
            var $el = $(sel);
            if ($el.length) {
                var val = $el.val();
                if ($el.is('select')) {
                    val = $el.find('option:selected').text();
                    if (val === 'Select' || val === 'Please Select' || val.indexOf('Select ') === 0) val = '';
                }
                var label = $el.closest('td').prev('th').text();
                label = $.trim(label.replace(/\s+/g, ' ').replace(/\*/g, '').replace(/:$/, ''));
                if (label && val && $.trim(val) !== '' && val !== 'N/A') {
                    generalInfo[label] = $.trim(val);
                }
            }
        });

        // Helper to extract fields from a container
        function extractFields($container) {
            var fields = {};
            if (!$container || !$container.length) return fields;

            $container.find(':input').each(function () {
                var inputtype = $(this).prop('type');
                var notallowed = ['hidden', 'file', 'submit', 'button'];
                if ($.inArray(inputtype, notallowed) !== -1) return;

                var val = $(this).val();
                if (inputtype === 'checkbox' || inputtype === 'radio') {
                    if ($(this).is(':checked')) {
                        val = $(this).parent('label').text().trim() || $(this).val() || 'Yes';
                    } else {
                        return; // skip unchecked
                    }
                } else if (inputtype === 'select-one' || inputtype === 'select-multiple') {
                    val = $(this).find('option:selected').text();
                    if (!val || val === 'Select' || val === 'Please Select' || val.indexOf('Select ') === 0) {
                        return;
                    }
                }

                if (!val || $.trim(val) === '' || val === 'N/A') return;

                var label = '';
                var $td = $(this).closest('td');
                if ($td.length) {
                    var $th = $td.prev('th');
                    if ($th.length) {
                        label = $th.text();
                    }
                }
                if (!label) {
                    var $fg = $(this).closest('.form-group');
                    if ($fg.length) {
                        label = $fg.find('label').first().text();
                    }
                }
                if (!label) {
                    var $lbl = $(this).closest('label');
                    if ($lbl.length) label = $lbl.text();
                }
                if (!label) {
                    label = $(this).attr('placeholder') || $(this).attr('name') || '';
                }

                label = $.trim(label.replace(/\s+/g, ' ').replace(/\*/g, '').replace(/:$/, ''));
                if (label && val) {
                    fields[label] = $.trim(val);
                }
            });
            return fields;
        }

        // 3. Gather Requests
        var requests = [];

        // Complaint #1
        var cat1 = $('#complaint_category option:selected').text().trim();
        if (cat1 === 'Select Category' || !$('#complaint_category').val()) cat1 = '';
        var type1 = $('#complaint_type option:selected').text().trim();
        if (type1 === 'Select Complaint' || !$('#complaint_type').val()) type1 = '';
        var detail1 = $('[name="complaint_details"]').val() || '';

        var fields1 = extractFields($('#issue_extra'));
        var cl1 = extractFields($('#issue_check_list'));
        $.extend(fields1, cl1);

        if (cat1 || type1 || $.trim(detail1) || Object.keys(fields1).length > 0) {
            requests.push({
                title: 'Complaint #1',
                category: cat1,
                type: type1,
                detail: $.trim(detail1),
                fields: fields1
            });
        }

        // Additional Complaints
        $('#additional_complaints_container .complaint-request-header-row').each(function (i) {
            var idx = $(this).data('index');
            var displayTitle = $(this).find('span').text().trim() || ('Complaint #' + (i + 2));
            var $selectRow = $('tr.complaint-request-select-row[data-index="' + idx + '"]');
            var cat = $selectRow.find('.extra-complaint-category option:selected').text().trim();
            if (cat === 'Select Category' || !$selectRow.find('.extra-complaint-category').val()) cat = '';
            var type = $selectRow.find('.extra-complaint-type option:selected').text().trim();
            if (type === 'Select Complaint' || !$selectRow.find('.extra-complaint-type').val()) type = '';

            var detail = $('tr.complaint-request-details-row[data-index="' + idx + '"]').find('.extra-complaint-details').val() || '';
            detail = $.trim(detail);
            if (!detail) {
                detail = $.trim($('[name="complaint_details"]').val());
            }

            var fields = extractFields($('#extra_complaint_extra_' + idx));
            var cl = extractFields($('#extra_complaint_checklist_' + idx));
            $.extend(fields, cl);

            if (cat || type || detail || Object.keys(fields).length > 0) {
                requests.push({
                    title: displayTitle,
                    category: cat,
                    type: type,
                    detail: detail,
                    fields: fields
                });
            }
        });

        var payload = {
            _token: '{{ csrf_token() }}',
            customer_info: customerInfo,
            general_info: generalInfo,
            requests: requests
        };

        $.ajax({
            url: "{{ url('/Supports/PrintForm/complaint') }}",
            type: "POST",
            dataType: "html",
            data: payload,
            beforeSend: function () {
                overlay('show');
            },
            success: function (data) {
                overlay('hide');
                window.open("{{ url('/Supports/PrintForm/complaint') }}");
            },
            error: function (data) {
                overlay('hide');
                customAlert('Error', 'Something went wrong. Please Contact with Administrator', 'red');
            }
        });
      });
      $('#complaint_type').on('change', function(e){
        var complaint_id = $('#complaint_type').val();
        if (complaint_id && complaint_id.length > 0) {
            var isDup = false;
            var dupNum = null;
            $('.extra-complaint-type').each(function(i) {
                if ($(this).val() == complaint_id) {
                    isDup = true;
                    dupNum = i + 2;
                    return false;
                }
            });
            if (isDup) {
                // alert('এই কমপ্লেইনটি ইতোমধ্যেই Complaint #' + dupNum + '-এ সিলেক্ট করা আছে। একই কমপ্লেইন একাধিকবার সিলেক্ট করা যাবে না।');
                customAlert('Warning', 'This complaint is already selected under Complaint #' + dupNum + '. The same complaint cannot be selected more than once.', 'yellow');
                $('#complaint_type').val('');
                if ($.fn.select2) $('#complaint_type').trigger('change.select2');
                $('#issue_extra').html(null);
                $('#issue_check_list').html(null);
                $('#attachment_item').html(null);
                e.stopImmediatePropagation();
                return false;
            }
        }
      });
      $('#complaint_type').on('change', function(){
        var complaint_id = $('#complaint_type').val();
        var type="complaint";
        var group_id= '{{user_permission()->group_info_id}}';
        if(complaint_id.length > 0){
          $.post('{{ url('workflow-attachment') }}', {_token:'{{ csrf_token() }}', complaint_id:complaint_id,type:type,group_id:group_id}, function(data){
            $('#attachment_item').html(data);
            $('#attachment_item').find('input[type="file"]').each(function() {
                $(this).attr('name', 'file_name[0][]');
            });
          });
        }
        else{
          //$('#attachment_item').html(null);
        }
      });
	  $('#complaint_type').on('change', function(){
        //console.log('op');
        var issue_id = $('#complaint_type').val();
        var type="complaint";
        var group_id= '{{user_permission()->group_info_id}}';
        if(issue_id.length > 0){
			//console.log('op');
          $.post('{{ url('issue-extra-form') }}', {_token:'{{ csrf_token() }}', issue_id:issue_id}, function(data){
			  //alert(data);
            $('#issue_extra').html(data);

          });
        }
        else{
          $('#issue_extra').html(null);
        }
      });


      $('#complaint_type').on('change', function(){
            //console.log('op');
            var issue_id = $('#complaint_type').val();
            var type="complaint";
            var group_id= '{{user_permission()->group_info_id}}';
            if(issue_id.length > 0){
                $.post('{{ url('issue-check-list') }}', {_token:'{{ csrf_token() }}', issue_id:issue_id}, function(data){
                    //alert(data);
                    $('#issue_check_list').html(data);

                });
            }
            else{
                $('#issue_check_list').html(null);
            }
        });
        $('#complaint_type').on('change', function(){
            var issue_id = $('#complaint_type').val();
            if(issue_id.length > 0){
                $.ajax({
                    url: base_url+'/CIFModification/is-inquiry-api/'+ issue_id,
                    type: "GET",
                    success: function (data) {
                        if (data == 1) {
                            $("#isInquiryApi").show();
                            $('#isInquiryApi').data('id',issue_id);
                        } else {
                            $("#isInquiryApi").hide();
                            $('#isInquiryApi').data('id',null);
                        }
                    }
                });
            }
        });
        $('#isInquiryApi').on('click', function (){
            var issue_id = $(this).data('id');
            var acc_no = "{{ (!empty($dataForView["account_number"])) ? $dataForView["account_number"] : 0 }}";
            var ref_no = "{{ (!empty($dataForView["reference_number"])) ? $dataForView["reference_number"] : 0 }}";
            var cif_no = "{{ (!empty($dataForView["CIF_number"])) ? $dataForView["CIF_number"] : 0 }}";
            if(issue_id.length > 0) {
                inquiryAPI(issue_id, acc_no, ref_no, cif_no);
            }
        });
        function inquiryAPI(issue_id, acc_no, ref_no, cif_no) {
            $.ajax({
                url: base_url + '/CIFModification/inquiry/' + issue_id + '/' + acc_no + '/' + ref_no + '/' + cif_no,
                type: "GET",
                beforeSend: function () {
                    overlay('show');
                },
                success: function (response) {
                    console.log(response);
                    overlay('hide');
                    if(response.status === 1) {
                        $('#inquiryModal').modal('show');
                        var helpers = '';
                        $.each(response.data, function(key, value) {
                            helpers += '<div class="col-lg-6">'+key+'</div><div class="col-lg-6">'+value+'</div>';
                        });
                        document.getElementById('inquiryData').innerHTML = helpers;
                    } else {
                        customAlert('Error', response.data, 'red');
                    }
                },
                error: function (error) {
                    console.log(error);
                    overlay('hide');
                    customAlert('Error', 'Something went wrong. Please Contact with Administrator', 'red');
                }
            });
        };
        var complaint_category = $('#complaint_category').val();
        var product_type = $('#product_type').val();

        getGetComplaintOptions(complaint_category,product_type);
        $('#complaint_category').on('change', function(){
            var complaint_category = $('#complaint_category').val();
            var product_type = $('#product_type').val();
            getGetComplaintOptions(complaint_category,product_type);

        });

    function getGetComplaintOptions(complaint_category,product_type) {
        var postBackCompType = "{{$pbCompType}}";

        var complaint_id = postBackCompType;
        var type="complaint";
        var group_id= '{{user_permission()->group_info_id}}';
        if(complaint_id.length > 0){
          $.post('{{ url('workflow-attachment') }}', {_token:'{{ csrf_token() }}', complaint_id:complaint_id,type:type,group_id:group_id}, function(data){
            $('#attachment_item').html(data);
            $('#attachment_item').find('input[type="file"]').each(function() {
                $(this).attr('name', 'file_name[0][]');
            });
          });
        }
        //alert(base_url+'/get-category-wise-complaint/'+ product_type+'/'+ complaint_category);
        if (complaint_category) {
            $.ajax({
                url: base_url+'/get-category-wise-complaint/'+ product_type+'/'+ complaint_category,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $('#complaint_type').html('<option value="">Select Complaint Type</option>');
                    $.each(data, function (key, value) {
                        var selectedForPb = "";

                        if (postBackCompType == value.id) {
                            selectedForPb = "selected";
                        }

                        $('#complaint_type').append('<option value="' + value.id + '" '+selectedForPb+' >' + value.name + '</option>');
                    });
                }
            });
        }
    };
    });
  </script>

  <script>
    $(document).ready(function() {
        var complaintCategoriesList = [
            @if(isset($complaintCategoryResult) && !empty($complaintCategoryResult))
                @foreach($complaintCategoryResult as $allComCategory)
                    { id: "{{ $allComCategory->id }}", name: "{{ addslashes($allComCategory->name) }}" },
                @endforeach
            @endif
        ];

        var complaintBlockCounter = 1;

        function isFieldRequired($input) {
            if ($input.prop('required') || $input.attr('required')) {
                return true;
            }
            var $td = $input.closest('td');
            var $prevTh = $td.prev('th');
            if ($prevTh.length && $prevTh.find('.required').text().indexOf('*') !== -1) {
                return true;
            }
            var $label = $td.find('label');
            if ($label.length && $label.find('.required').text().indexOf('*') !== -1) {
                return true;
            }
            return false;
        }

        function prefixInputsForBlock($container, blockIdx) {
            $container.find('input, select, textarea').each(function() {
                var $el = $(this);
                var oldName = $el.attr('name');
                if (oldName && !oldName.startsWith('multi_requests[')) {
                    if (oldName.indexOf('[') !== -1) {
                        var firstBracket = oldName.indexOf('[');
                        var prefix = oldName.substring(0, firstBracket);
                        var rest = oldName.substring(firstBracket);
                        $el.attr('name', 'multi_requests[' + blockIdx + '][fields][' + prefix + ']' + rest);
                    } else {
                        $el.attr('name', 'multi_requests[' + blockIdx + '][fields][' + oldName + ']');
                    }
                }
            });
        }

        function prefixChecklistsForBlock($container, blockIdx) {
            $container.find('input, select, textarea').each(function() {
                var $el = $(this);
                var oldName = $el.attr('name');
                if (oldName && !oldName.startsWith('multi_requests[')) {
                    $el.attr('name', 'multi_requests[' + blockIdx + '][check_lists][' + oldName + ']');
                }
            });
        }

        function addComplaintBlock(idx, savedData) {
            var currentReqNum = $('#additional_complaints_container .complaint-request-header-row').length + 2;

            var catOptions = '<option value="">Select Category</option>';
            $.each(complaintCategoriesList, function(i, item) {
                var sel = (savedData && savedData.complaint_category == item.id) ? 'selected' : '';
                catOptions += '<option value="' + item.id + '" ' + sel + '>' + item.name + '</option>';
            });

            var savedDetails = (savedData && savedData.complaint_details) ? savedData.complaint_details : '';

            var html = `
                <tr class="complaint-request-header-row" data-index="${idx}" style="background: #fcf8e3; border-top: 3px solid #f0ad4e;">
                    <td colspan="4" style="padding: 7px 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: bold; color: #8a6d3b; font-size: 13px;">
                                <i class="fa fa-folder-open"></i> Complaint #${currentReqNum}
                            </span>
                            <button type="button" class="btn btn-xs btn-danger btn-remove-complaint" data-index="${idx}">
                                <i class="fa fa-trash"></i> Remove
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="complaint-request-select-row" data-index="${idx}">
                    <th class="vcenter">Complaint Category <span class="required">*</span></th>
                    <td class="vcenter">
                        <select class="form-control extra-complaint-category select2" name="multi_requests[${idx}][complaint_category]" data-index="${idx}">
                            ${catOptions}
                        </select>
                    </td>
                    <th class="vcenter">Complaint Sub Category <span class="required">*</span></th>
                    <td class="vcenter">
                        <select class="form-control extra-complaint-type select2" name="multi_requests[${idx}][complaint_type]" data-index="${idx}">
                            <option value="">Select Complaint</option>
                        </select>
                    </td>
                </tr>
                <tr class="complaint-request-details-row" data-index="${idx}">
                    <th class="vcenter">Complaint Detail</th>
                    <td class="vcenter" colspan="3">
                        <textarea class="form-control extra-complaint-details" name="multi_requests[${idx}][complaint_details]" rows="2" placeholder="Complaint Detail (Leave blank to use primary detail)">${savedDetails}</textarea>
                    </td>
                </tr>
                <tr class="extra-issue-extra" id="extra_complaint_extra_${idx}" data-index="${idx}"></tr>
                <tr class="extra-issue-checklist" id="extra_complaint_checklist_${idx}" data-index="${idx}"></tr>
                <tr class="extra-complaint-attachment" id="extra_complaint_attachment_${idx}" data-index="${idx}">
                    <th class="vcenter">Attachments
                        <div class="clearfix"></div>
                        <small class="error-message">(Max file size is {{ $fileSizeLimit }} MB)</small>
                    </th>
                    <td class="vcenter extra-attachment-item" id="extra_attachment_item_${idx}" colspan="3">
                        <small class="text-muted"><i class="fa fa-info-circle"></i> Please select Complaint Sub Category to see required attachments.</small>
                    </td>
                </tr>
            `;

            $('#additional_complaints_container').append(html);
            if ($.fn.select2) {
                $('#additional_complaints_container tr[data-index="' + idx + '"] select.select2').select2();
            }

            if (savedData && savedData.complaint_category) {
                loadCategoryComplaints(idx, savedData.complaint_category, savedData.complaint_type, savedData);
            }
        }

        function loadCategoryComplaints(blockIndex, catId, selectedComplaintId, savedData) {
            var productType = $('#product_type').val();
            var $typeSelect = $('tr.complaint-request-select-row[data-index="' + blockIndex + '"]').find('.extra-complaint-type');
            $typeSelect.html('<option value="">Select Complaint</option>');
            $('#extra_complaint_attachment_' + blockIndex).find('.extra-attachment-item').html('<small class="text-muted"><i class="fa fa-info-circle"></i> Please select Complaint Sub Category to see required attachments.</small>');

            if (catId) {
                $.ajax({
                    url: base_url + '/get-category-wise-complaint/' + productType + '/' + catId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var options = '<option value="">Select Complaint</option>';
                        $.each(data, function(key, value) {
                            var sel = (selectedComplaintId && selectedComplaintId == value.id) ? 'selected' : '';
                            options += '<option value="' + value.id + '" ' + sel + '>' + value.name + '</option>';
                        });
                        $typeSelect.html(options);
                        if ($.fn.select2) {
                            $typeSelect.select2();
                        }
                        if (selectedComplaintId) {
                            loadComplaintFields(blockIndex, selectedComplaintId, savedData);
                        }
                    }
                });
            }
        }

        function loadComplaintAttachment(blockIndex, issueId) {
            var $attContainer = $('#extra_attachment_item_' + blockIndex);
            if (issueId && issueId.length > 0) {
                $.post('{{ url("workflow-attachment") }}', {
                    _token: '{{ csrf_token() }}',
                    complaint_id: issueId,
                    type: 'complaint',
                    group_id: '{{ user_permission()->group_info_id }}'
                }, function(data) {
                    $attContainer.html(data);
                    $attContainer.find('input[type="file"]').each(function(fileIdx) {
                        $(this).attr('name', 'file_name[' + blockIndex + '][]');
                        var img = $attContainer.find('img').eq(fileIdx);
                        var btn = $attContainer.find('a').eq(fileIdx);
                        var imgId = 'imgInDynamic_' + blockIndex + '_' + fileIdx;
                        var btnId = 'downloadBtn_' + blockIndex + '_' + fileIdx;
                        if (img.length) img.attr('id', imgId);
                        if (btn.length) btn.attr('id', btnId);
                        $(this).off('change').on('change', function() {
                            var file = this.files[0];
                            var imgEl = document.getElementById(imgId);
                            var btnEl = document.getElementById(btnId);
                            if (file) {
                                if (file.type.startsWith('image/')) {
                                    if (imgEl) { imgEl.style.display = 'block'; imgEl.src = window.URL.createObjectURL(file); }
                                    if (btnEl) { btnEl.style.display = 'none'; btnEl.href = ''; }
                                } else {
                                    if (imgEl) { imgEl.style.display = 'none'; imgEl.src = ''; }
                                    if (btnEl) {
                                        btnEl.style.display = 'block';
                                        btnEl.href = window.URL.createObjectURL(file);
                                        btnEl.innerHTML = 'Download ' + file.name;
                                    }
                                }
                            } else {
                                if (imgEl) { imgEl.style.display = 'none'; imgEl.src = ''; }
                                if (btnEl) { btnEl.style.display = 'none'; btnEl.href = ''; }
                            }
                        });
                    });
                });
            } else {
                $attContainer.html('<small class="text-muted"><i class="fa fa-info-circle"></i> Please select Complaint Sub Category to see required attachments.</small>');
            }
        }

        function loadComplaintFields(blockIndex, issueId, savedData) {
            loadComplaintAttachment(blockIndex, issueId);

            if (issueId && issueId.length > 0) {
                overlay('show');
                $.post('{{ url("issue-extra-form") }}', {
                    _token: '{{ csrf_token() }}',
                    issue_id: issueId
                }, function(data) {
                    var $container = $('#extra_complaint_extra_' + blockIndex);
                    $container.html(data);
                    prefixInputsForBlock($container, blockIndex);

                    // If we have saved field values (from page reload), populate them
                    if (savedData && savedData.fields) {
                        $.each(savedData.fields, function(fieldName, fieldVal) {
                            var $fieldInput = $container.find('[name="multi_requests[' + blockIndex + '][fields][' + fieldName + ']"]');
                            if ($fieldInput.length) {
                                if ($fieldInput.is(':checkbox') || $fieldInput.is(':radio')) {
                                    $fieldInput.filter('[value="' + fieldVal + '"]').prop('checked', true);
                                } else {
                                    $fieldInput.val(fieldVal);
                                }
                            }
                        });
                    }

                    if (typeof flatpickr !== 'undefined') {
                        $container.find('.flatpickr, .datePicker, .js-date').flatpickr({
                            dateFormat: "d-m-Y"
                        });
                    }
                    overlay('hide');
                }).fail(function() {
                    overlay('hide');
                });

                $.post('{{ url("issue-check-list") }}', {
                    _token: '{{ csrf_token() }}',
                    issue_id: issueId
                }, function(data) {
                    var $clContainer = $('#extra_complaint_checklist_' + blockIndex);
                    $clContainer.html(data);
                    prefixChecklistsForBlock($clContainer, blockIndex);

                    if (savedData && savedData.check_lists) {
                        $.each(savedData.check_lists, function(clName, clVal) {
                            var $clInput = $clContainer.find('[name="multi_requests[' + blockIndex + '][check_lists][' + clName + ']"]');
                            if ($clInput.length && clVal) {
                                $clInput.prop('checked', true);
                            }
                        });
                    }
                });
            } else {
                $('#extra_complaint_extra_' + blockIndex).html('');
                $('#extra_complaint_checklist_' + blockIndex).html('');
            }
        }

        $('#btn_add_complaint_request').on('click', function() {
            complaintBlockCounter++;
            addComplaintBlock(complaintBlockCounter, null);
        });

        // On additional category change
        $(document).on('change', '.extra-complaint-category', function() {
            var blockIndex = $(this).data('index');
            var catId = $(this).val();
            $('#extra_complaint_extra_' + blockIndex).html('');
            $('#extra_complaint_checklist_' + blockIndex).html('');
            loadCategoryComplaints(blockIndex, catId, null, null);
        });

        // On additional complaint type change
        $(document).on('change', '.extra-complaint-type', function(e) {
            var $this = $(this);
            var blockIndex = $this.data('index');
            var issueId = $this.val();

            if (issueId && issueId.length > 0) {
                var isDup = false;
                var dupText = '';

                // Check Complaint #1
                var req1Val = $('#complaint_type').val();
                if (req1Val && req1Val == issueId) {
                    isDup = true;
                    dupText = 'Complaint #1';
                }

                // Check other extra complaints
                if (!isDup) {
                    $('.extra-complaint-type').each(function() {
                        var otherIdx = $(this).data('index');
                        if (otherIdx != blockIndex && $(this).val() == issueId) {
                            isDup = true;
                            var headerText = $('tr.complaint-request-header-row[data-index="' + otherIdx + '"]').find('span').text().trim();
                            dupText = headerText || ('Complaint #' + otherIdx);
                            return false;
                        }
                    });
                }

                if (isDup) {
                    // alert('এই কমপ্লেইনটি ইতোমধ্যেই ' + dupText + '-এ সিলেক্ট করা আছে। একই কমপ্লেইন একাধিকবার সিলেক্ট করা যাবে না।');
                    customAlert('Warning', 'This complaint is already selected in ' + dupText + '. The same complaint cannot be selected more than once.', 'yellow');
                    $this.val('');
                    if ($.fn.select2) {
                        $this.trigger('change.select2');
                    }
                    $('#extra_complaint_extra_' + blockIndex).html('');
                    $('#extra_complaint_checklist_' + blockIndex).html('');
                    $('#extra_complaint_attachment_' + blockIndex).find('.extra-attachment-item').html('<small class="text-muted"><i class="fa fa-info-circle"></i> Please select Complaint Sub Category to see required attachments.</small>');
                    e.stopImmediatePropagation();
                    return false;
                }
            }

            loadComplaintFields(blockIndex, issueId, null);
        });

        // Remove additional complaint block
        $(document).on('click', '.btn-remove-complaint', function() {
            var blockIndex = $(this).data('index');
            $('tr.complaint-request-header-row[data-index="' + blockIndex + '"]').remove();
            $('tr.complaint-request-select-row[data-index="' + blockIndex + '"]').remove();
            $('tr.complaint-request-details-row[data-index="' + blockIndex + '"]').remove();
            $('#extra_complaint_extra_' + blockIndex).remove();
            $('#extra_complaint_checklist_' + blockIndex).remove();
            $('#extra_complaint_attachment_' + blockIndex).remove();
            updateComplaintHeaders();
        });

        function updateComplaintHeaders() {
            var reqNum = 2;
            $('#additional_complaints_container .complaint-request-header-row').each(function() {
                $(this).find('span').html('<i class="fa fa-folder-open"></i> Complaint #' + reqNum);
                reqNum++;
            });
        }

        // Restore from old input if page reloaded after validation error
        var oldMultiData = $('#multi_requests_data').val();
        if (oldMultiData) {
            try {
                var parsedOld = JSON.parse(oldMultiData);
                if (Array.isArray(parsedOld) && parsedOld.length > 1) {
                    for (var i = 1; i < parsedOld.length; i++) {
                        complaintBlockCounter = Math.max(complaintBlockCounter, i);
                        addComplaintBlock(i, parsedOld[i]);
                    }
                }
            } catch(e) {
                console.error("Failed to restore old multi requests", e);
            }
        }

        // Clear errors on input
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).css('border-color', '');
            $(this).siblings('.client-validation-error').remove();
        });

        // On form submit - CLIENT SIDE PRE-VALIDATION
        $('#mainForm').on('submit', function(e) {
            $('.client-validation-error').remove();
            $('input, select, textarea').css('border-color', '');

            var hasError = false;
            var firstErrorElem = null;

            function flagError($elem, msg) {
                hasError = true;
                $elem.css('border-color', '#d9534f');
                if ($elem.siblings('.client-validation-error').length === 0) {
                    $elem.after('<span class="client-validation-error" style="color:#d9534f; font-weight:bold; font-size:12px; display:block; margin-top:3px;"><i class="fa fa-exclamation-circle"></i> ' + msg + '</span>');
                }
                if (!firstErrorElem) {
                    firstErrorElem = $elem;
                }
            }

            // 1. Check Request 1 Details, Category & Type
            var details0 = $('[name="complaint_details"]').val();
            if (!details0 || $.trim(details0) === '') {
                flagError($('[name="complaint_details"]'), 'Complaint Detail is required (Complaint #1)');
            }

            var cat0 = $('#complaint_category').val();
            if (!cat0) {
                flagError($('#complaint_category'), 'Complaint Category is required (Complaint #1)');
            }
            var type0 = $('#complaint_type').val();
            if (!type0) {
                flagError($('#complaint_type'), 'Complaint Sub Category is required (Complaint #1)');
            }

            // Check required fields in Request 1
            $('#issue_extra').find('input, select, textarea').each(function() {
                var $el = $(this);
                if ($el.is(':hidden') && !$el.hasClass('select2-hidden-accessible')) return;
                if (isFieldRequired($el) && (!$el.val() || $.trim($el.val()) === '')) {
                    flagError($el, 'This field is required (Complaint #1)');
                }
            });

            // 2. Check Additional Requests
            var additionalRows = $('#additional_complaints_container .complaint-request-header-row');
            additionalRows.each(function(i) {
                var idx = $(this).data('index');
                var displayNum = i + 2;
                var $catSelect = $('tr.complaint-request-select-row[data-index="' + idx + '"]').find('.extra-complaint-category');
                var $typeSelect = $('tr.complaint-request-select-row[data-index="' + idx + '"]').find('.extra-complaint-type');

                if (!$catSelect.val()) {
                    flagError($catSelect, 'Complaint Category is required (Complaint #' + displayNum + ')');
                }
                if (!$typeSelect.val()) {
                    flagError($typeSelect, 'Complaint Sub Category is required (Complaint #' + displayNum + ')');
                }

                // Check required dynamic fields in this additional block
                $('#extra_complaint_extra_' + idx).find('input, select, textarea').each(function() {
                    var $el = $(this);
                    if ($el.is(':hidden') && !$el.hasClass('select2-hidden-accessible')) return;
                    if (isFieldRequired($el) && (!$el.val() || $.trim($el.val()) === '')) {
                        flagError($el, 'This field is required (Complaint #' + displayNum + ')');
                    }
                });
            });

            // 3. Duplicate Complaint Check across all complaints
            var selectedTypes = {};
            if (type0) {
                selectedTypes[type0] = 'Complaint #1';
            }
            additionalRows.each(function(i) {
                var idx = $(this).data('index');
                var displayNum = i + 2;
                var $typeSelect = $('tr.complaint-request-select-row[data-index="' + idx + '"]').find('.extra-complaint-type');
                var val = $typeSelect.val();
                if (val) {
                    if (selectedTypes[val]) {
                        flagError($typeSelect, 'Duplicate complaint! Already selected in ' + selectedTypes[val]);
                    } else {
                        selectedTypes[val] = 'Complaint #' + displayNum;
                    }
                }
            });

            // If client validation failed, stop submit! DO NOT RELOAD!
            if (hasError) {
                overlay('hide');
                e.preventDefault();
                if (firstErrorElem) {
                    $('html, body').animate({
                        scrollTop: firstErrorElem.offset().top - 120
                    }, 500);
                }
                alert('অনুগ্রহ করে চিহ্নিত আবশ্যকীয় ফিল্ডগুলো পূরণ করুন!');
                return false;
            }

            // If valid, pack multi_requests_data JSON
            var multiRequests = [];

            // Complaint 0
            var req0 = {
                complaint_category: $('#complaint_category').val(),
                complaint_type: $('#complaint_type').val(),
                complaint_details: $('[name="complaint_details"]').val(),
                fields: {},
                check_lists: {}
            };
            $('#issue_extra').find(':input').each(function() {
                var name = $(this).attr('name');
                if (!name) return;
                if ($(this).is(':checkbox') || $(this).is(':radio')) {
                    if ($(this).is(':checked')) req0.fields[name] = $(this).val();
                } else {
                    req0.fields[name] = $(this).val();
                }
            });
            $('#issue_check_list').find(':input').each(function() {
                var name = $(this).attr('name');
                if (!name) return;
                if ($(this).is(':checkbox')) {
                    if ($(this).is(':checked')) req0.check_lists[name] = $(this).val();
                } else {
                    req0.check_lists[name] = $(this).val();
                }
            });
            multiRequests.push(req0);

            // Additional complaints
            additionalRows.each(function() {
                var idx = $(this).data('index');
                var cat = $('tr.complaint-request-select-row[data-index="' + idx + '"]').find('.extra-complaint-category').val();
                var type = $('tr.complaint-request-select-row[data-index="' + idx + '"]').find('.extra-complaint-type').val();
                var details = $('tr.complaint-request-details-row[data-index="' + idx + '"]').find('.extra-complaint-details').val();

                var reqItem = {
                    complaint_category: cat,
                    complaint_type: type,
                    complaint_details: details || $('[name="complaint_details"]').val(),
                    fields: {},
                    check_lists: {}
                };
                $('#extra_complaint_extra_' + idx).find(':input').each(function() {
                    var name = $(this).attr('name');
                    if (!name) return;
                    var cleanName = name;
                    var match = name.match(/\[fields\]\[([^\]]+)\]/);
                    if (match) cleanName = match[1];
                    if ($(this).is(':checkbox') || $(this).is(':radio')) {
                        if ($(this).is(':checked')) reqItem.fields[cleanName] = $(this).val();
                    } else {
                        reqItem.fields[cleanName] = $(this).val();
                    }
                });
                $('#extra_complaint_checklist_' + idx).find(':input').each(function() {
                    var name = $(this).attr('name');
                    if (!name) return;
                    var cleanName = name;
                    var match = name.match(/\[check_lists\]\[([^\]]+)\]/);
                    if (match) cleanName = match[1];
                    if ($(this).is(':checkbox')) {
                        if ($(this).is(':checked')) reqItem.check_lists[cleanName] = $(this).val();
                    } else {
                        reqItem.check_lists[cleanName] = $(this).val();
                    }
                });
                multiRequests.push(reqItem);
            });

            if (multiRequests.length > 1) {
                $('#multi_requests_data').val(JSON.stringify(multiRequests));
            } else {
                $('#multi_requests_data').val('');
            }

            return true;
        });
    });
  </script>
@endsection
