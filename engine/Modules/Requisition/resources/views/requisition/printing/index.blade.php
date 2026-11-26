@extends('requisition::layouts.master')
@section('content')
<div class="mt-3">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Printing Stationery Requisitions</h6>
        </div>
        @include('requisition::requisition.list-menu')
        <div class="card-body">
            <form method="GET" action="{{ route('requisition.printing-item-list') }}">
                <div class="d-flex w-100 mb-3">
                    <input type="text" name="search" class="form-control form-control-sm me-2 flex-grow-1"
                        id="search" placeholder="Search Requisition Number"
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
            <div class="table-responsive">
                <table class ="table table-striped table-condensed">
                    {{-- <colgroup>
                        <col width="10%">
                        <col width="20%">
                        <col width="10%">
                        <col width="20%">
                        <col width="20%">
                        <col width="30%">
                    </colgroup> --}}
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="vcenter text-center">Requisition Number</th>
                            <th class="vcenter text-center">Logger</th>
                            <th class="vcenter text-center"> Log Date & Time</th>
                            <th class="vcenter text-center">Specification</th>
                            <th class="vcenter text-center">Status</th>
                            <th class="vcenter text-center">Remark</th>
                            <th class="vcenter text-center">
                                <a href="{{ route('requisition.create-printing-stationery') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle"></i> Create</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="word-break: break-all;">
                        @if($tblData->count() > 0)
                        @foreach($tblData as $key => $data)
                            @php
                                $status = '';

                                if ($data['status'] == 1){
                                    $status = "Pending";
                                }elseif ($data['status'] == 2){
                                    $status = "Draft";
                                }

                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="vcenter text-center"> <a target="_blank" href="{{ route('requisition.printing-item.details', $data['requisition_number']) }}">{{ $data['requisition_number'] }}</a> </td>
                                <td class="vcenter text-center"> {{ $data['user']['user_id'] }} </td>
                                <td class="vcenter text-center"> {{  $data['created_at'] }}</td>
                                <td class="vcenter text-center"> {{ $data['specification'] }}</td>
                                <td class="vcenter text-center"> {{ $status }}</td>
                                <td class="vcenter text-center"> {{ $data['remark'] }}</td>
                                <td class="vcenter text-center">
                                    <a href="{{ route('requisition.printing-item.edit', $data['requisition_number']) }}" class="btn btn-success btn-sm gradient ajax_page" title="Edit" escape="false"> <i class="fa fa-pencil"></i> Edit</a>
                                    @if($data['status'] == 2) {{-- draft --}}
                                        <a href="{{ route('requisition.printing-item.publish', $data['requisition_number']) }}" class="btn btn-secondary btn-sm gradient ajax_page" title="Edit" escape="false"> <i class="fa fa-upload"></i> Publish</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @else <tr> <td class="vcenter text-center" colspan="8"> <strong>Data Not Available</strong></td> </tr>
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
@endsection
