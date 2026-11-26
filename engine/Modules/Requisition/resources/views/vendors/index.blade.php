@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item active">Vendor List</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class ="{{ !empty($tblData) ? 'commonDataTableAllAsc' : '' }} table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class=" text-white requ-th-color">#</th>
                            <th class="vcenter text-center text-white requ-th-color">Name</th>
                            <th class="vcenter text-center text-white requ-th-color">Email</th>
                            <th class="vcenter text-center text-white requ-th-color">Phone</th>
                            <th class="vcenter text-center text-white requ-th-color">Address</th>
                            <th class="vcenter text-center text-white requ-th-color">Status</th>
                            <th class="vcenter text-center text-white requ-th-color">
                                <a href="{{ route('requisition.vendor.create') }}" class="btn btn-sm btn-primary gradient ajax_page" title="Add" escape="false"> <i class="fa fa-plus"></i> Add New</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="word-break: break-all;">
                        @if(!empty($tblData))
                        @foreach($tblData as $key => $data)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td class="vcenter text-center"> {{ $data['name'] }} </td>
                                <td class="vcenter text-center"> {{ $data['email'] }} </td>
                                <td class="vcenter text-center"> {{ $data['phone'] }} </td>
                                <td class="vcenter text-center"> {{ $data['office_address'] }} </td>
                                <td class="vcenter text-center">{{ $data['status_name'] }}</td>
                                <td class="vcenter actions text-center">
                                    <a href="{{ route('requisition.vendor.products', $data['id']) }}" class="btn btn-warning btn-sm">Products</a>
                                    @if($data['status'] == '0')
                                        <a href="{{ route('requisition.vendor.status', [$data['id'], '1']) }}" class="btn btn-sm btn-info gradient" title="Active" escape="false"> <i class="fa fa-check"></i> Active</a>
                                    @elseif($data['status'] == '1')
                                        <a href="{{ route('requisition.vendor.edit', $data['id']) }}" class="btn btn-sm btn-success gradient ajax_page" title="Edit" escape="false"> <i class="fa fa-pencil"></i> Edit</a>
                                        <a href="{{ route('requisition.vendor.status', [$data['id'], '0']) }}" class="btn btn-sm btn-danger gradient" title="Inactive" escape="false"> <i class="fa fa-times"></i> Inactive</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @else <tr> <td class="vcenter text-center" colspan="6"> <strong>Data Not Available</strong></td> </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
