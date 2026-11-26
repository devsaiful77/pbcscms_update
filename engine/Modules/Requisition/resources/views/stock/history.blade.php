@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <h3>Item Stock History</h3>
    <div class="table-responsive">
        <table class ="commonDataTableAllAsc table table-bordered table-striped table-hover">
            <colgroup>
                <col width="3%">
                <col width="20%">
                <col width="10%">
                <col width="20%">
                <col width="10%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th>#</th>
                    <th class="vcenter text-center">Product Name</th>
                    <th class="vcenter text-center">Unit Type</th>
                    <th class="vcenter text-center">Create User</th>
                    <th class="vcenter text-center">Challan No</th>
                    <th class="vcenter text-center">Quantity</th>
                    <th class="vcenter text-center">Created Time</th>
                </tr>
            </thead>
            <tbody style="word-break: break-all;">
                @if(!empty($tblData))
                @foreach($tblData as $key => $data)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td class="vcenter text-center"> {{ $data['requ_item']['product_name'] }} </td>
                        <td class="vcenter text-center"> {{ $data['requ_item']['unit_type'] }} </td>
                        <td class="vcenter text-center"> {{ $data['create_id'] }} </td>
                        <td class="vcenter text-center"> {{ $data['challan_no'] }} </td>
                        <td class="vcenter text-center">{{ $data['quantity'] }}</td>
                        <td class="vcenter text-center"> {{ \Carbon\Carbon::parse($data['created_at'])->format('d-m-Y h:i:s') }}</td>
                    </tr>
                @endforeach
                @else <tr> <td class="vcenter text-center" colspan="4"> <strong>Data Not Available</strong></td> </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
