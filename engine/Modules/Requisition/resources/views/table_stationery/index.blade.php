@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item">Mangage Item</li>
            <li class="breadcrumb-item active">Table Stationary Items</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="{{ !empty($tableStationaries) ? 'commonDataTableAllAsc' : '' }} table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="vcenter text-white requ-th-color">#</th>
                            <th class="vcenter text-white requ-th-color">Product Name</th>
                            <th class="vcenter text-white requ-th-color">Unit Type</th>
                            <th class="vcenter text-white requ-th-color">Unit Price</th>
                            <th class="vcenter text-center text-white requ-th-color" style="text-align:left;">
                                <a class="btn btn-success ajax_page gradient btn-sm" href="{{ route('requisition.table-stationery-item.create') }}"><i class="fa fa-plus-circle"></i> Add Items</a>
                            </th>
                        </tr>
                    </thead>
                    @if(!empty($tableStationaries))
                        @foreach ($tableStationaries as $key => $item)
                            <tbody>
                                <tr>
                                    <td class="vcenter">{{ $key + 1 }}</td>
                                    <td class="vcenter">
                                        @php
                                            $image = !empty($item['image']) ? asset('public/items/' . $item['image']) : asset('public/images/placeholder-image.png');
                                        @endphp

                                        <img src="{{ $image }}" width="40" class="hover-fullscreen-trigger" data-full="{{ $image }}">
                                        {{ $item['product_name'] }}</td>
                                    <td class="vcenter">
                                        {{ $item['unit']['name'] }}
                                    </td>
                                    <td class="vcenter">
                                        {{ $item['unit_price'] ?? 'N / A' }}
                                    </td>
                                    <td class="vcenter text-center">
                                        {{-- <a class="btn btn-danger ajax_page gradient btn-sm" href="{{ route('requisition.items.edit', $item->id) }}"><i class="fa fa-edit"></i> Delete</a> --}}
                                        @if ($item['status'] == 0)
                                        <a href="{{ route('requisition.table-stationery-item.status', [$item['id'], '1']) }}" class="btn btn-info gradient btn-sm" title="Active" escape="false"> <i class="fa fa-check"></i> Active</a>
                                        @elseif($item['status'] == 1)
                                        <a class="btn btn-primary ajax_page gradient btn-sm" href="{{ route('requisition.table-stationery-item.edit', $item->id) }}"><i class="fa fa-edit"></i> Edit</a>
                                        <a href="{{ route('requisition.table-stationery-item.status', [$item['id'], '0']) }}" class="btn btn-danger gradient btn-sm" title="Inactive" escape="false"> <i class="fa fa-times"></i> Inactive</a>
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
<div id="fullscreen-hover-preview" style="display: none;">
    <img src="" alt="Preview">
</div>

@endsection
