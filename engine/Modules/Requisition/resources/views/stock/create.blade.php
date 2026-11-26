@php
    $layout = ($type == 'reqAdminMaker')
        ? 'requisition::layouts.master'
        : 'layouts.admin';

    $dashboardUrl = ($type == 'reqAdminMaker')
        ? '/requisition/dashboard'
        : '/';

    $routeParams = $type == 'reqAdminMaker'
                    ? ['type' => 'reqAdminMaker']
                    : [];
@endphp
@extends($layout)
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url($dashboardUrl) }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('requisition.stock.list', $routeParams) }}">Stock List</a></li>
            <li class="breadcrumb-item active">Printing Stationery Stock Add</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="row">
        <div class="col-md-12">
            <div class="border rounded p-3 shadow-sm bg-light">
                @if(!empty($requisitionItems) && count($requisitionItems) > 0)
                    <label for="requisition-select" class="form-label fw-semibold">Select Requisition Items</label>
                    <select class="form-select js-example-basic-multiple select2" id="requisition-select" name="states[]" multiple="multiple">
                        @foreach ($requisitionItems as $item)
                            <option value="{{ $item['id'] }}" data-name="{{ $item['product_name'] }}" data-image="{{ $item['image'] }}" data-low_stock_alert_qty="{{ $item['low_stock_alert_qty'] ?? '' }}">
                                {{ $item['product_name'] }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-exclamation-circle"></i> No items found.
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Dynamic Quantity Input Form -->
    <form action="{{ route('requisition.stock.store', $routeParams) }}" method="POST">
        @csrf
        <div id="selected-items-form" class="mt-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Selected Items
                </div>
                <div class="card-body">
                    <div>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>S/L</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Low Stock Alert Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="items-container">

                            </tbody>

                            <tbody>
                                @php
                                    $oldProducts = old('item_ids', []);
                                    $oldQuantities = old('quantities', []);
                                    $low_stock_alert_qty = old('low_stock_alert_qty', []);
                                @endphp

                                @forelse($oldProducts as $index => $product_id)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <select name="item_ids[]" class="form-select form-select-sm" style="width: 100%;">
                                            <option value="">Select Product</option>
                                            @foreach ($requisitionItems as $item)
                                                <option value="{{ $item->id }}"
                                                    data-name="{{ $item->product_name }}"
                                                    {{ $item->id == $product_id ? 'selected' : '' }}>
                                                    {{ $item->product_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('item_ids.' . $index)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" name="quantities[]" placeholder="Quantity" class="form-control form-control-sm" value="{{ $oldQuantities[$index] ?? '' }}">
                                        @error('quantities.' . $index)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" name="low_stock_alert_qty[]" placeholder="Quantity" class="form-control form-control-sm" value="{{ $oldQuantities[$index] ?? '' }}">
                                        @error('low_stock_alert_qty.' . $index)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                </tr>
                                @empty
                                    <!-- No old input; JS will add one row on document ready -->
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Vendor <span class="text-danger">*</span></label>
                            <select name="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Challan No </label>
                            <input type="text" name="challan_no" class="form-control @error('challan_no') is-invalid @enderror" value="{{ old('challan_no') }}" placeholder="Enter Challan No">
                            @error('challan_no')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    const requisitionItems = @json($requisitionItems);
</script>

<script>
function openQuantityModal(itemId) {
    document.getElementById('item_id').value = itemId;
    var myModal = new bootstrap.Modal(document.getElementById('quantityModal'));
    myModal.show();
}
</script>
<script>
    $(document).ready(function () {
        $('.js-example-basic-multiple').select2({
            placeholder: "Select One",
            allowClear: true
        });

        $('#requisition-select').on('change', function () {
            const selected = $(this).find(':selected');
            const container = $('#items-container');
            container.empty();
            if (selected.length == 0) {
                container.append(`
                    <tr id="no-items-row">
                        <td colspan="3" class="text-center text-muted">No items selected.</td>
                    </tr>
                `);
                return;
            }
            selected.each(function (index, element) {
                const id = $(element).val();
                const name = $(element).data('name');
                const lowStockQty = $(element).data('low_stock_alert_qty') || '';
                const image = $(element).data('image');
                const imageUrl = "{{ asset('public/items') }}/" + image;

                const rowHtml = `
                     <tr>
                        <td>${index + 1}</td>
                        <td>
                            <input type="hidden" name="item_ids[]" value="${id}">
                            <span class="fw-semibold"><img src="${imageUrl}" width="30"> ${name}</span>
                        </td>
                        <td>
                            <input type="number" name="quantities[${id}]" class="form-control" placeholder="Enter quantity" min="1" required>
                        </td>
                        <td>
                            <input type="number" name="low_stock_alert_qty[${id}]" class="form-control" placeholder="Enter low stock quantity" min="1" value="${lowStockQty}" required>
                        </td>
                    </tr>

                `;
                container.append(rowHtml);
            });
        });
    });
</script>
@endsection

 {{-- <tr>
    <td>${index + 1}</td>
    <td>
        <select name="item_ids[]" class="form-select form-select-sm" style="width: 100%;">
            <option value="">Select Product</option>
            ${requisitionItems.map(item => `
                <option value="${item.id}" ${item.id == id ? 'selected' : ''}>
                    ${item.product_name}
                </option>
            `).join('')}
        </select>
    </td>
    <td>
        <input type="number" name="quantities[]" class="form-control" placeholder="Quantity *" min="1" required>
    </td>
</tr> --}}
