@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">Region</li>
        </ol>
    </nav>
</div>

<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="{{ !empty($regions) ? 'commonDataTableAllAsc' : '' }} table table-bordered table-condensed table-hover">
                    <colgroup>
                        <col width="5%"></col>
                        <col width="30%"></col>
                        <col width="10%"></col>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="vcenter text-white requ-th-color">#</th>
                            <th class="vcenter text-white requ-th-color">Region Name</th>
                            <th class="vcenter text-white text-center requ-th-color" style="text-align:left;">
                                <a class="btn btn-success ajax_page gradient btn-sm" href="{{ route('requisition.region.create') }}"><i class="fa fa-plus-circle"></i> Add Region</a>
                            </th>
                        </tr>
                    </thead>
                    @if(!empty($regions))
                        @foreach ($regions as $key => $item)
                            <tbody>
                                <tr>
                                    <td class="vcenter">{{ $key + 1 }}</td>
                                    <td class="vcenter">{{ $item['name'] }}</td>
                                    <td class="vcenter text-center">
                                        @if ($item['status'] == 0)
                                        <a href="{{ route('requisition.region.status', [$item['id'], '1']) }}" class="btn btn-info gradient btn-sm" title="Active" escape="false"> <i class="fa fa-check"></i> Active</a>
                                        @elseif($item['status'] == 1)
                                        <a class="btn btn-warning ajax_page gradient btn-sm" href="{{ route('requisition.region.view', $item->id) }}"><i class="fa fa-eye"></i> View</a>
                                        <a class="btn btn-primary ajax_page gradient btn-sm" href="{{ route('requisition.region.edit', $item->id) }}"><i class="fa fa-edit"></i> Edit</a>
                                        {{-- <a href="{{ route('requisition.region.status', [$item['id'], '0']) }}" class="btn btn-danger gradient btn-sm" title="Inactive" escape="false"> <i class="fa fa-times"></i> Inactive</a> --}}
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    @else
                        <tr> <th class="vcenter text-center" colspan="3">No items found</th> </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
