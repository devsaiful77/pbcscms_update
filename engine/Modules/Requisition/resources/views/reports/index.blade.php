@extends('requisition::layouts.master')
@section('content')
<style>
    .select2-container {
        width: 100% !important;
    }
    /* Match Bootstrap form-control height */
    .select2-container .select2-selection--single {
        border: 1px solid #000 !important;
        height: 35px !important;
        padding: 5px 0px !important;
    }

    /* Center the text vertically */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
    }

    /* Adjust the arrow alignment */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 35px !important;
    }

    /* Apply 1px solid black border to all form controls */
    .form-control,
    .form-select {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }

    /* Arrow when closed (pointing down) */
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-style: solid !important;
        border-color: #000 transparent transparent transparent !important;
    }

    /* Arrow when open (pointing up) */
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-style: solid !important;
        border-color: transparent transparent #000 transparent !important;
    }

    /* For multi-select */
    .select2-container .select2-selection--multiple {
        border: 1px solid #000 !important;
        min-height: 35px !important;
        padding: 3px !important;
    }

    /* Fix the height of selected tags */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin-top: 7px !important;
    }

    /* Make the input inside multi-select aligned */
    .select2-container--default .select2-selection--multiple .select2-search__field {
        height: 18px !important;
        margin-top: 6px !important;
    }



</style>
<div class="mt-3">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Reports</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('requisition.report.index') }}" method="get">
                <div class="row">
                    <div class="form-group col-4">
                        <label for="report_type" class="form-label"><strong>Report Type</strong></label>
                        <select name="report_type" id="report_type" class="form-select report_type">
                            @foreach($reportType as $key => $value)
                                <option value="{{ $key }}"
                                    {{ (!empty($searchDataForView['report_type']) && $searchDataForView['report_type'] == $key) ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Always visible for product_quantity --}}
                    <div class="form-group col-4 report-common-field">
                        <label for="date_from" class="form-label"><strong>Date From</strong></label>
                        <input type="text" name="date_from" class="form-control dtPickerThreeCls readonly datePickerThreeFrom" style="display: none;" placeholder="Date From" value="{{ $searchDataForView['date_from'] }}" autocomplete="off">
                        <input type="text" name="date_from" class="form-control dtPickerHistoryCls readonly datePickerHistoryFrom" style="display: none;" placeholder="Date From" value="{{ $searchDataForView['date_from'] }}" autocomplete="off" disabled>
                    </div>
                    <div class="form-group col-4 report-common-field">
                        <label for="date_to" class="form-label"><strong>Date To</strong></label>
                        <input type="text" name="date_to" class="form-control dtPickerThreeCls readonly datePickerThreeTo" style="display: none;" placeholder="Date To" value="{{ $searchDataForView['date_to'] }}" autocomplete="off">
                        <input type="text" name="date_to" class="form-control dtPickerHistoryCls readonly datePickerHistoryTo" style="display: none;" placeholder="Date To" value="{{ $searchDataForView['date_to'] }}" autocomplete="off" disabled>
                    </div>

                    {{-- These will be hidden for product_quantity --}}
                    <div class="form-group col-4 report-other-field">
                        <label for="requisition_number" class="form-label"><strong>Requisition Number</strong></label>
                        <input type="text" name="requisition_number" class="form-control" id="requisition_number" placeholder="Requisition Number" value="{{ $searchDataForView['requisition_number'] }}">
                    </div>
                    <div class="form-group col-4 report-other-field">
                        <label for="user_id" class="form-label"><strong>User ID</strong></label>
                        <input type="text" name="user_id" class="form-control" id="user_id" placeholder="User ID" value="{{ $searchDataForView['user_id'] }}">
                    </div>
                    <div class="form-group col-4 report-other-field">
                        <label for="mobile_number" class="form-label"><strong>Mobile Number</strong></label>
                        <input type="text" name="mobile_number" class="form-control" id="mobile_number" placeholder="Mobile Number" value="{{ $searchDataForView['mobile_number'] }}">
                    </div>
                    <div class="form-group col-4 report-other-field">
                        <label for="requisition_status" class="form-label"><strong>Requisition Status</strong></label>
                        <select name="requisition_status[]" id="requisition_status" class="form-select select2" multiple="multiple">
                            <option value="">-- Select Requisition Status --</option>
                            @foreach($requisitionStatus as $key => $value)
                                <option value="{{ $key }}"
                                    {{ (!empty($searchDataForView['requisition_status']) && in_array($key, $searchDataForView['requisition_status'])) ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-4 report-other-field">
                        <label for="requisition_type" class="form-label"><strong>Requisition Type</strong></label>
                        <select name="requisition_type[]" id="requisition_type" class="form-select select2" multiple="multiple">
                            <option value="">-- Select Requisition Type --</option>
                            @foreach($requisitionType as $key => $value)
                                <option value="{{ $key }}"
                                    {{ (!empty($searchDataForView['requisition_type']) && in_array($key, $searchDataForView['requisition_type'])) ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-4 report-other-field">
                        <label for="subgroup_id" class="form-label"><strong>Subgroup</strong></label>
                        <select name="subgroup_id[]" id="subgroup_id" class="form-select select2" multiple="multiple">
                            <option value="">-- Select Subgroup --</option>
                            @foreach($subGroups as $value)
                                <option value="{{ $value->id }}"
                                    @if(!empty($searchDataForView['subgroup_id']) && in_array($value->id, $searchDataForView['subgroup_id']))
                                        selected
                                    @endif
                                >
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-4 report-other-field">
                        <label for="region_id" class="form-label"><strong>Region</strong></label>
                        <select name="region_id[]" id="region_id" class="form-select select2" multiple="multiple">
                            <option value="">-- Select Region --</option>
                            @foreach($regions as $value)
                                <option value="{{ $value->id }}"
                                    @if(!empty($searchDataForView['region_id']) && in_array($value->id, $searchDataForView['region_id']))
                                        selected
                                    @endif
                                >
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-4 report-other-field">
                        <label for="vendor_id" class="form-label"><strong>Vendor</strong></label>
                        <select name="vendor_id[]" id="vendor_id" class="form-select select2" multiple="multiple">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $value)
                                <option value="{{ $value->id }}"
                                    @if(!empty($searchDataForView['vendor_id']) && in_array($value->id, $searchDataForView['vendor_id']))
                                        selected
                                    @endif
                                >
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-4 report-other-field">
                        <label for="delivery_status" class="form-label"><strong>Delivery Status</strong></label>
                        <select name="delivery_status[]" id="delivery_status" class="form-select select2" multiple="multiple">
                            <option value="">-- Select Delivery Status --</option>
                            <option value="delivered"
                                {{ !empty($searchDataForView['delivery_status']) && in_array('delivered', $searchDataForView['delivery_status']) ? 'selected' : '' }}>
                                Delivered
                            </option>
                            <option value="not_delivered"
                                {{ !empty($searchDataForView['delivery_status']) && in_array('not_delivered', $searchDataForView['delivery_status']) ? 'selected' : '' }}>
                                Not Delivered
                            </option>
                        </select>
                    </div>

                    {{-- Always visible for product_quantity --}}
                    <div class="form-group col-4 report-common-field">
                        <label for="item_id" class="form-label"><strong>Requisition Items</strong></label>
                        <select name="item_id[]" id="item_id" class="form-select select2" multiple="multiple">
                            <option value="">-- Select Item --</option>
                            @foreach($items as $value)
                                <option value="{{ $value->id }}"
                                    @if(!empty($searchDataForView['item_id']) && in_array($value->id, $searchDataForView['item_id']))
                                        selected
                                    @endif
                                >
                                    {{ implode(' - ', array_filter([$value->product_code, $value->product_name])) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-12 mt-3">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a href="{{ route('requisition.report.index') }}" class="btn btn-warning ms-1"><i class="fa fa-eraser"></i> Reset</a>
                        <button type="submit" name="export" value="export_to_excel" class="btn btn-success ms-1"><i class="fa fa-file-excel-o"></i> Export</button>
                        <span class="text-danger" style="font-size: 16px;"><i class="fa fa-arrow-left"></i> For detailed report press Export</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">
            @php
                $reportTitle = '';
                if(empty($searchDataForView['report_type']) || ($searchDataForView['report_type'] == 'gen_report')){
                    $reportTitle = 'General Report';
                }elseif($searchDataForView['report_type'] == 'handler_report'){
                    $reportTitle = 'Handler Report';
                }elseif($searchDataForView['report_type'] == 'history_report'){
                    $reportTitle = 'History Report';
                }elseif($searchDataForView['report_type'] == 'product_quantity'){
                    $reportTitle = 'Product Quantity Report';
                }else{
                    $reportTitle = 'Report';
                }
            @endphp
            <h6 class="card-title mb-0">{{ $reportTitle . ' Results' }}</h6>
        </div>
        <div class="card-body">
            @if(empty($searchDataForView['report_type']) || ($searchDataForView['report_type'] == 'gen_report'))
                @include('requisition::reports.includes.general_report')
            @elseif($searchDataForView['report_type'] == 'handler_report')
                @include('requisition::reports.includes.handler_report')
            @elseif($searchDataForView['report_type'] == 'history_report')
                @include('requisition::reports.includes.history_report')
            @elseif($searchDataForView['report_type'] == 'product_quantity')
                @include('requisition::reports.includes.product_qty_report')
            @endif
        </div>
    </div>

    <small class="text-left"><strong>Script Execution Time (Sec.) : </strong> {{ !empty($processing_time) ? $processing_time : 'N/A' }}</small>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var customError = "<?php echo $customError; ?>";
        if(customError == 1){
            customAlert('Information','Please select a date.','red');
        }

        function toggleDatePicker(){
            var report_type = $(".report_type").val();

            $(".dtPickerThreeCls input, .dtPickerHistoryCls input").val("");

            if (report_type == 'history_report'){
                $(".dtPickerThreeCls").hide();
                $(".dtPickerThreeCls").prop('disabled',true);
                $(".dtPickerHistoryCls").show();
                $(".dtPickerHistoryCls").prop('disabled',false);
            }else if(report_type == 'handler_report'){
                $(".dtPickerHistoryCls").hide();
                $(".dtPickerHistoryCls").prop('disabled',true);
                $(".dtPickerThreeCls").show();
                $(".dtPickerThreeCls").prop('disabled',true);
            }else{
                $(".dtPickerHistoryCls").hide();
                $(".dtPickerHistoryCls").prop('disabled',true);
                $(".dtPickerThreeCls").show();
                $(".dtPickerThreeCls").prop('disabled',false);
            }
        }

        toggleDatePicker();

        function searchFieldToggle() {
            var requisition_type = $('#requisition_type').val() || [];

            var disableTypes = ['printingStationery', 'tableStationery'];
            var enableTypes  = ['repair', 'newAsset'];

            // ---- REGION TOGGLE ----
            if (requisition_type.length === 0) {
                $('#region_id').prop('disabled', false);
            }
            else if (disableTypes.some(type => requisition_type.includes(type))) {
                $('#region_id').prop('disabled', true);
            }
            else if (enableTypes.some(type => requisition_type.includes(type))) {
                $('#region_id').prop('disabled', false);
            }

            // ---- VENDOR TOGGLE ----
            if (requisition_type.includes('printingStationery')) {
                $('#vendor_id').prop('disabled', true);
            } else {
                $('#vendor_id').prop('disabled', false);
            }
        }


        searchFieldToggle();

        $(document).on('change', '#requisition_type', function(){
            searchFieldToggle();
        });

        $(document).on('change', '.report_type', function(){
            toggleDatePicker();
        });
    });
</script>

<script>
    // function toggleReportFields() {
    //     var reportType = $('#report_type').val();

    //     if (reportType === 'product_quantity') {
    //         $('.report-other-field').hide();
    //         $('.report-common-field').show();
    //     } else {
    //         $('.report-other-field').show();
    //         $('.report-common-field').show();
    //     }
    // }


    function toggleReportFields() {
        var reportType = $('#report_type').val();

        if (reportType === 'product_quantity') {
            // Hide other fields
            $('.report-other-field').hide();
            $('.report-common-field').show();

            // Clear values inside hidden fields so stale data isn't submitted
            $('.report-other-field').find('input[type="text"]').val('');
            $('.report-other-field').find('input[type="hidden"]').val('');

            // Clear select2 multi-selects properly
            $('.report-other-field').find('select.select2').val(null).trigger('change');

            // Clear plain selects (non select2) if any
            $('.report-other-field').find('select:not(.select2)').val('');

        } else {
            $('.report-other-field').show();
            $('.report-common-field').show();
        }
    }


    $(document).ready(function () {
        toggleReportFields();

        $('#report_type').on('change', function () {
            toggleReportFields();
        });
    });
</script>
@endpush
