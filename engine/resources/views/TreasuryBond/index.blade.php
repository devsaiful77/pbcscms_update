@extends('layouts.admin')
@section('content')
<div class="table-responsive">
    <table class ="commonDataTableAllAsc table table-bordered table-striped table-hover">
        <colgroup>
            <col width="25%">
            <col width="35%">
            <col width="25%">
            <col width="25%">
            @if($checker == false)
            <col width="15%">
            @endif
        </colgroup>
        <thead>
            <tr>
                <th class="vcenter text-center">Name</th>
                <th class="vcenter text-center">Week</th>
                <th class="vcenter text-center">Count</th>
                <th class="vcenter text-center">Status</th>
                @if($checker == false)
                <th class="vcenter text-left">
                    <a href="{{ url('/treasury-bond/add') }}" class="btn btn-primary gradient ajax_page" title="Add" escape="false"> <i class="fa fa-plus"></i> Add</a>
                </th>
                @endif
            </tr>
        </thead>
        <tbody style="word-break: break-all;">
            @IF(!empty($tblData))
            @FOREACH($tblData as $data)
                <tr>
                    <td class="vcenter text-center"> {{ $data['name'] }} </td>
                    <td class="vcenter text-center"> {{ $data['week'] }} </td>
                    <td class="vcenter text-center"> {{ $data['count'] }} </td>
                    <td class="vcenter text-center"> {{ $data['status_name'] }} </td>
                    @if($checker == false)
                    <td class="vcenter actions text-left">
                        @IF($data['status'] == '0')
                            <a href="{{ url('/treasury-bond/status/'.$data['id'].'/1') }}" class="btn btn-info gradient" title="Active" escape="false"> <i class="fa fa-check"></i> Active</a>
                        @ELSEIF($data['status'] == '1')

                        <?php
                            $editUrl = url('/treasury-bond/edit/'.$data['id']);
                            if (!empty($searchDataForView)) {
                                $editUrl .= '?'.http_build_query($searchDataForView);
                            }
                        ?>

                                <a href="{{$editUrl}}" class="btn btn-success gradient ajax_page" title="Edit" escape="false"> <i class="fa fa-pencil"></i> Edit</a>
                                <a href="{{ url('/treasury-bond/status/'.$data['id'].'/0') }}" class="btn btn-danger gradient" title="Inactive" escape="false"> <i class="fa fa-times"></i> Inactive</a>

                        @ENDIF
                    </td>
                    @endif
                </tr>
            @ENDFOREACH

            @ENDIF
        </tbody>
    </table>
</div>
@endsection
