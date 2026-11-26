@extends('layouts.admin')
@section('content')
<div class="table-responsive">
    <table class="commonDataTableAllAsc table table-bordered table-striped table-hover">
        <colgroup>
            <col width="8%">
            <col width="12%">
            <col width="25%">
            <col width="12%">
            <col width="23%">
            <col width="8%">
            @if($checker == false)
            <col width="12%">
            @endif 
        </colgroup>
        <thead>
            <tr>
                <th class="vcenter text-center">Short ID</th>
                <th class="vcenter text-center">Category Code</th>
                <th class="vcenter text-center">Title</th>
                <th class="vcenter text-center">Account Type</th>
                <th class="vcenter text-center">Description</th>
                <th class="vcenter text-center">Status</th>
                @if($checker == false)
                <th class="vcenter text-left"> 
                    <a href="{{ url('/AccountCategory/add') }}" class="btn btn-primary gradient ajax_page" title="Add" escape="false"> <i class="fa fa-plus"></i> Add</a>
                </th>
                @endif 
            </tr>
        </thead>
        <tbody style="word-break: break-all;">
            @if(!empty($tblData))
            @foreach($tblData as $data)
                <tr>
                    <td class="vcenter text-center"> {{ $data['short_id'] }} </td>
                    <td class="vcenter text-center"> {{ $data['categ_code'] }} </td>
                    <td class="vcenter text-left"> {{ $data['title'] }} </td>
                    <td class="vcenter text-center"> {{ $data['ac_type'] }} </td>
                    <td class="vcenter text-left"> {{ $data['description'] }} </td>
                    <td class="vcenter text-center">
                        <span class="badge {{ $data['status'] == 1 ? 'badge-success bg-success' : 'badge-danger bg-danger' }}">
                            {{ $data['status_name'] }}
                        </span>
                    </td>
                    @if($checker == false)
                    <td class="vcenter actions text-left">
                        @if($data['status'] == '0')
                            <a href="{{ url('/AccountCategory/status/'.$data['id'].'/1') }}" class="btn btn-info gradient" title="Active" escape="false"> <i class="fa fa-check"></i> Active</a>
                        @elseif($data['status'] == '1')
                        <?php
                            $editUrl = url('/AccountCategory/edit/'.$data['id']);
                            if (!empty($searchDataForView)) {
                                $editUrl .= '?'.http_build_query($searchDataForView);
                            }
                        ?>
                            <a href="{{$editUrl}}" class="btn btn-success gradient ajax_page" title="Edit" escape="false"> <i class="fa fa-pencil"></i> Edit</a>
                            <a href="{{ url('/AccountCategory/status/'.$data['id'].'/0') }}" class="btn btn-danger gradient" title="Inactive" escape="false"> <i class="fa fa-times"></i> Inactive</a>
                        @endif
                    </td>
                    @endif
                </tr>
            @endforeach
            @else 
                <tr> <td class="vcenter text-center" colspan="{{ $checker == false ? 7 : 6 }}"> <strong>Data Not Available</strong></td> </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
