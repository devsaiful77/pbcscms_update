@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item">Mangage Item</li>
            <li class="breadcrumb-item active">Unit</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class ="{{ !empty($tblData) ? 'commonDataTableAllAsc' : '' }} table table-bordered table-condensed table-hover">
                    <colgroup>
                        <col width="3%">
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-white requ-th-color">#</th>
                            <th class="vcenter text-center text-white requ-th-color">Unit Name</th>
                            <th class="vcenter text-center text-white requ-th-color">Status</th>
                            <th class="vcenter text-center text-white requ-th-color">
                                <a href="{{ route('requisition.unit.create') }}" class="btn btn-primary gradient ajax_page btn-sm" title="Add" escape="false"> <i class="fa fa-plus"></i> Add</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="word-break: break-all;">

                        @if(!empty($tblData))
                        @foreach($tblData as $key => $data)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td class="vcenter text-center"> {{ $data['name'] }} </td>
                                <td class="vcenter text-center">{{ $data['status_name'] }}</td>
                                <td class="vcenter actions text-center">
                                    @if($data['status'] == '0')
                                        <a href="{{ route('requisition.unit.status', [$data['id'], '1']) }}" class="btn btn-info gradient btn-sm" title="Active" escape="false"> <i class="fa fa-check"></i> Active</a>
                                    @elseif($data['status'] == '1')
                                        <a href="{{ route('requisition.unit.edit', $data['id']) }}" class="btn btn-success gradient ajax_page btn-sm" title="Edit" escape="false"> <i class="fa fa-pencil"></i> Edit</a>
                                        <a href="{{ route('requisition.unit.status', [$data['id'], '0']) }}" class="btn btn-danger gradient btn-sm" title="Inactive" escape="false"> <i class="fa fa-times"></i> Inactive</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @else <tr> <td class="vcenter text-center" colspan="4"> <strong>Data Not Available</strong></td> </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
