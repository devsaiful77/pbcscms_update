@php
    $layout = ($type == 'reqAdminMaker')
        ? 'requisition::layouts.master'
        : 'layouts.admin';

    $dashboardUrl = ($type == 'reqAdminMaker')
        ? '/requisition/dashboard'
        : '/';
@endphp

@extends($layout)

@section('content')
<style>
    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }

    .stockAlertBG-blink {
        background-color: #f59696 !important;
        /* animation: blinker 1s linear infinite; */
    }
</style>
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">

            <li class="breadcrumb-item"><a href="{{ url($dashboardUrl) }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Printing Stationery Stock List</li>
            @php
                $routeParams = $type == 'reqAdminMaker'
                    ? ['type' => 'reqAdminMaker']
                    : [];
            @endphp

            <a href="{{ route('requisition.stock', $routeParams) }}"
            class="btn btn-primary btn-sm ms-auto">
                <i class="fa fa-plus-circle"></i> Add Stock
            </a>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class ="commonDataTableAllAsc table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            {{-- <th>S/L</th> --}}
                            <th class="vcenter text-center requ-th-color text-white">Product Name</th>
                            <th class="vcenter text-center requ-th-color text-white">Unit</th>
                            <th class="vcenter text-center requ-th-color text-white">Current Stock</th>
                            <th class="vcenter text-center requ-th-color text-white">Low Stock Alert Quantity</th>
                        </tr>
                    </thead>
                    <tbody style="word-break: break-all;">
                        @if(!empty($tblData))
                        @foreach($tblData as $key => $data)
                        @php
                            $styleColor = "";
                            if($data['quantity'] <= $data['low_stock_alert_qty']){
                                $styleColor = 'stockAlertBG-blink';
                            }
                        @endphp
                            @if (!empty($data['requ_item']['product_name']))
                                <tr>
                                    {{-- <td>{{ $key+1 }}</td> --}}
                                    <td class="vcenter {{ $styleColor }}"><img src="{{URL::asset('public/items/'.$data['requ_item']['image'])}}" alt="" width="40" srcset=""> {{ $data['requ_item']['product_name'] }}</td>
                                    <td class="vcenter text-center {{ $styleColor }}">{{ $data['requ_item']['unit']['name'] }}</td>
                                    <td class="vcenter text-center {{ $styleColor }}">{{ $data['quantity'] }}</td>
                                    <td class="vcenter text-center {{ $styleColor }}">{{ $data['low_stock_alert_qty'] }}</td>
                                </tr>
                            @endif
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
