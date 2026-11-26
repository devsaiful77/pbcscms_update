@extends('layouts.print')
@section('content')
    @php
        $subgroupStr = '';
        $subgroupList = (!empty(Auth::user()->user_unit)) ? Auth::user()->user_unit->subgroup_info_id : '' ;
        if (!empty($subgroupList)) {
            $subgroupArr = explode(',', $subgroupList);
            $subgroup = DB::table('subgroup_info')->select('id','name')->whereIn('id',$subgroupArr)->pluck('name')->toArray();
            if(!empty($subgroup)){
                $subgroupStr = implode(',', $subgroup);
            }
        }
    @endphp
    <div class="my-1" style="border: 1px solid #000;">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6 pt-1">
                    <p style="font-size: 14px;"class="pt-2 m-0">To</p>
                    <h6 style="font-size: 14px;">The Manager</h6>
                    <h6 style="font-size: 14px;">{{ $subgroupStr }} </h6>
                    <h6 style="font-size: 14px;">Prime Bank PLC.</h6>
                </div>
                <div class="col-md-6 text-right">
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/Logo-en.png') }}" alt="Prime Bank PLC" style="height: 38px;"/>
                    <p style="padding-right: 1rem;"><b>Date:</b> {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
                </div>
            </div>

            <div class="row justify-content-center" style="margin-top: -2rem;">
                <h4 class="text-center" style="font-size: 1.5rem !important;"><b>
                    @if($stype == 'complaint')
                        Digital Complaint Form
                    @elseif($stype == 'service')
                        Digital Service Request Form
                    @else
                        Non Customer Form
                    @endif
                </b></h4>
            </div>

            <div class="border-all" style="page-break-inside: avoid;">
                <table class="table table-bordered table-condensed table">
                    <colgroup>
                        <col width="35%">
                        <col width="65%">
                    </colgroup>
                    @if(!empty($customer_info))
                        <tr><th colspan="2" class="text-center graybg"><h4 style="font-size: 1rem !important;"><b>Customer Information</b></h4></th></tr>
                        @foreach($customer_info as $key => $value)
                            @if(!empty($value) && $value != 'N/A' && $key != 'Customer_Name')
                                <tr>
                                    <th>{{ str_replace('_',' ',$key) }}</th>
                                    <td>{{ $value }}</td>
                                </tr>
                            @elseif($key == 'Customer_Name' && empty($customer_info['Customer Name']))
                                <tr>
                                    <th>Customer Name</th>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </table>
            </div>

            @if(!empty($general_info) && is_array($general_info))
                @php
                    $hasGeneral = false;
                    foreach($general_info as $gk => $gv) {
                        if (!empty($gv) && $gv != 'N/A' && $gv != 'Please Select' && $gv != 'Select') {
                            $hasGeneral = true;
                            break;
                        }
                    }
                @endphp
                @if($hasGeneral)
                    <div class="border-all mt-3" style="page-break-inside: avoid;">
                        <table class="table table-bordered table-condensed table">
                            <colgroup>
                                <col width="35%">
                                <col width="65%">
                            </colgroup>
                            <tr>
                                <th colspan="2" class="text-center graybg">
                                    <h4 style="font-size: 1rem !important; margin: 3px 0;"><b>Additional Information</b></h4>
                                </th>
                            </tr>
                            @foreach($general_info as $gKey => $gVal)
                                @if(!empty($gVal) && $gVal != 'N/A' && $gVal != 'Please Select' && $gVal != 'Select')
                                    <tr>
                                        <th>{{ str_replace('_', ' ', $gKey) }}</th>
                                        <td>{{ $gVal }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                @endif
            @endif

            @if(!empty($requests) && is_array($requests))
                @foreach($requests as $req)
                    <div class="border-all mt-3" style="page-break-inside: avoid;">
                        <table class="table table-bordered table-condensed table">
                            <colgroup>
                                <col width="35%">
                                <col width="65%">
                            </colgroup>
                            <tr>
                                <th colspan="2" class="text-center graybg">
                                    <h4 style="font-size: 1rem !important; margin: 3px 0;">
                                        <b>{{ $req['title'] ?? (($stype == 'complaint') ? 'Complaint' : 'Service Request') }}</b>
                                    </h4>
                                </th>
                            </tr>
                            @if(!empty($req['category']))
                                <tr>
                                    <th>{{ ($stype == 'complaint') ? 'Complaint Category' : 'Service Category' }}</th>
                                    <td>{{ $req['category'] }}</td>
                                </tr>
                            @endif
                            @if(!empty($req['type']))
                                <tr>
                                    <th>{{ ($stype == 'complaint') ? 'Complaint Sub Category' : 'Service Sub Request Type' }}</th>
                                    <td>{{ $req['type'] }}</td>
                                </tr>
                            @endif
                            @if(!empty($req['detail']) && $req['detail'] != 'N/A')
                                <tr>
                                    <th>Complaint Detail</th>
                                    <td>{{ $req['detail'] }}</td>
                                </tr>
                            @endif
                            @if(!empty($req['fields']) && is_array($req['fields']))
                                @foreach($req['fields'] as $fKey => $fVal)
                                    @if(!empty($fVal) && $fVal != 'N/A')
                                        <tr>
                                            <th>{{ str_replace('_', ' ', $fKey) }}</th>
                                            <td>{{ $fVal }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </table>
                    </div>
                @endforeach
            @elseif(!empty($information_update))
                <div class="border-all mt-3" style="page-break-inside: avoid;">
                    <table class="table table-bordered table-condensed table">
                        <colgroup>
                            <col width="35%">
                            <col width="65%">
                        </colgroup>
                        <tr><th colspan="2" class="text-center graybg"><h4 style="font-size: 1rem !important;"><b>Information Update</b></h4></th></tr>
                        @foreach($information_update as $key => $value)
                            @if(!empty($value) && ($value != 'N/A'))
                                <tr>
                                    <th>{{ str_replace('_',' ',$key) }}</th>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            @endif

            <div class="mt-2 mx-2">
                <p style="font-size: 0.7rem !important; margin-bottom: 0!important;">
                    <b><u>
                        Note: I/We declare that the above information provided by me/us are correct & do authorize the Bank to execute the same.
                    </u></b>
                </p>
            </div>

            <!-- Signature Section -->
            <div class="mt-5">
                <div class="col-md-6-for">
                    <div class="border-top-color pt-2">
                        <p style="font-weight: 500;"class="m-0">Customer Signature</p>
                        <p class="m-0" style="font-size: 12px">{{ $customer_info['Customer_Name'] ?? ($customer_info['Customer Name'] ?? 'NA') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-0 mt-4">
                <!-- Bank Use Only Section -->
                <div class="border">
                    <h6 class="text-center" style="font-size: 14px !important; font-width:bold"><b>Bank Use Only</b></h6>
                </div>
                <div class="border p-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="confirmed1">
                        <label class="form-check-label" style="font-size: 14px !important;" for="confirmed1">Physical Presence</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="confirmed2">
                        <label class="form-check-label" style="font-size: 14px !important;" for="confirmed2">Signature & Contact detail has been verified from bank records</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="confirmed3">
                        <label class="form-check-label" style="font-size: 14px !important; padding-right: 5px" for="confirmed3">Customer signed in my presence</label>
                    </div>
                    <input class="form-check-input" type="checkbox" id="supportingDocs">
                    <label class="form-check-label" style="font-size: 14px !important;" for="supportingDocs">Supporting documents (if any) .................................</label>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="row-for-footer text-center pt-3 mt-4">
                <div class="col-md-4-for-footer">
                    <div class="border-top-color pt-2">
                        <p style="font-weight: 500;"class="m-0">RM Signature</p>
                        <p style="font-size: 0.9rem;" class="m-0">Name:  {{Auth::user()->name}}</p>
                        <p style="font-size: 0.9rem;" class="m-0">Date: {{ \Carbon\Carbon::now()->format('d-m-Y') }} , Emp ID: {{Auth::user()->emp_id}}</p>
                    </div>
                </div>
                <div class="col-md-8-for-footer">
                    <div class="border-top-color pt-2">
                        <p style="font-weight: 500;" class="m-0">Operation Manager/Head of Branch/Division</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
