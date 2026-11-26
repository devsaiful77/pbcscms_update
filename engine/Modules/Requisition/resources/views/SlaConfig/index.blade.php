@extends('layouts.admin')
@section('content')
<style>
    .btn-color{
        background-color: #375eb1;
        color:aliceblue;
        border-radius: 0;
    }
    .btn-color.active {
        background-color: #166834 !important;
        border-color: #166834 !important;
        color: #fff !important;
    }

</style>
@php
    $redirectUrl = '';
    $requisitionType = '';
    if ($action == 'gss_index') {
        $requisitionType = 'General Support Service';
        $redirectUrl = route('requisition.sla.gss.index');
    } elseif ($action == 'pi_index') {
        $requisitionType = 'Printing Item';
        $redirectUrl = route('requisition.sla.pi.index');
    } elseif ($action == 'npi_index') {
        $requisitionType = 'Non Printing Item';
        $redirectUrl = route('requisition.sla.npi.index');
    } else {
        $requisitionType = '';
    }
@endphp
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">SLA Config</li>
        </ol>
    </nav>
</div>

<div class="mt-3">
    <div class="card p-3 mb-3">
        <div class="d-flex gap-2">
            <a href="{{ route('requisition.sla.gss.index') }}" class="btn btn-color {{ $action == 'gss_index' ? 'active' : '' }}" style="width: 240px;">General Support Service</a>
            <a href="{{ route('requisition.sla.pi.index') }}" class="btn btn-color {{ $action == 'pi_index' ? 'active' : '' }}" style="width: 240px;">Printing Item</a>
            <a href="{{ route('requisition.sla.npi.index') }}" class="btn btn-color {{ $action == 'npi_index' ? 'active' : '' }}" style="width: 240px;">Non Printing Item</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisition.sla.store-update') }}" method="post">
                @csrf
                <input type="hidden" name="redirectUrl" value="{{ $redirectUrl }}">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="vcenter text-center text-white requ-th-color">Role</th>
                                <th class="vcenter text-center text-white requ-th-color">Requisition Type</th>
                                <th class="vcenter text-center text-white requ-th-color">SLA Time (In Min)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($req_roles as $item)
                                @php
                                    $slaToPrint = "";
                                    foreach ($sla_config as $key => $value) {
                                        if($item['name'] == $value['role_name']){
                                            $slaToPrint = $value['sla_time'];
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" value="{{ $item['display_name'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="{{ $requisitionType }}" readonly>
                                        <input type="hidden" name="requisition_type" value="{{ $requisitionType }}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" min="0" name="sla_time[{{ $item['name'] }}]" value="{{ $slaToPrint }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" class="btn btn-color">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
