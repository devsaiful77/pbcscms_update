@extends('requisition::layouts.master')

@section('content')
<link rel="stylesheet" href="{{ URL::asset('public/css/requisition/requisition_form.css') }}">
<div class="mt-3">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Edit Non-Printing Item Requisition</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('requisition.table-stationery.update', $requisition->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group col-12">
                    <fieldset class="border scheduler-border">
                        <div class="scheduler-border">
                            <p class="float-none">Initiator Information</p>
                        </div>
                        <table class="table custom-border">
                            @php
                                $subgroupList = Auth::user()->user_unit->subgroup_info_id ?? 'N/A';
                                $subgroupStr = "";
                                $subgroup = DB::table('subgroup_info')->whereIn('id', explode(',', $subgroupList))->pluck('name')->toArray();

                                $subgroupStr = implode(',', $subgroup);
                            @endphp
                            <thead>
                            <tr>
                                <th width="30%" class="py-1">Phone Number</th>
                                <th width="30%" class="py-1">Branch Name</th>
                                <th width="40%">Address</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="number" name="i_phone" class="form-control form-control-sm" value="{{ Auth::user()->mobile_no ?? 'N/A' }}">
                                </td>
                                <td>
                                    <input type="hidden" name="i_branch_id" value="{{ $subgroupList ?? 'N/A' }}">
                                    <input type="text" class="form-control form-control-sm" value="{{$subgroupStr ?? 'N/A'}}" readonly>
                                </td>
                                <td>
                                    <textarea name="i_address" class="form-control form-control-sm" rows="1">{{$requisition->i_address}}</textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
                <div class="form-group col-12">
                    <fieldset class="border scheduler-border">
                        <div class="scheduler-border">
                            <p class="float-none">Add Product</p>
                        </div>
                        <table class="table custom-border" id="productTable">
                            <thead>
                                <tr>
                                    <th class="py-2">SL</th>
                                    <th width="40%" class="py-2">Product Name</th>
                                    <th width="40%" class="py-2">Quantity</th>
                                    <th class="py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $oldProducts = old('product_ids', $requisition->tableStationeries->pluck('product_id')->toArray());
                                    $oldQuantities = old('quantities', $requisition->tableStationeries->pluck('quantity')->toArray());
                                @endphp

                                @forelse($oldProducts as $index => $product_id)
                                    <tr>
                                        <td class="sl"></td>
                                        <td>
                                            <select name="product_ids[]" class="form-select form-select-sm product-dropdown" style="width: 100%;">
                                                <option value="">Select Product</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{-- data-code="{{ $item->product_code }}" --}}
                                                        data-name="{{ $item->product_name }}"
                                                        data-unit="{{ $item->unit->name }}"
                                                        {{ $item->id == $product_id ? 'selected' : '' }}>
                                                        {{ $item->product_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('product_ids.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" name="quantities[]" placeholder="Quantity" class="form-control form-control-sm" value="{{ $oldQuantities[$index] ?? '' }}" min="1">
                                            @error('quantities.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- No old input; JS will add one row on document ready -->
                                @endforelse
                            </tbody>
                        </table>
                        <button type="button" id="addRow" class="btn btn-success float-end btn-sm"><i class="fa fa-plus"></i> Add More</button>
                    </fieldset>
                </div>
                <div class="form-group col-12">
                    <fieldset class="border scheduler-border">
                        <div class="scheduler-border">
                            <p class="float-none">File Attachment</p>
                        </div>
                        <table class="table custom-border" id="attachmentTable">
                            <thead>
                                <tr>
                                    <th class="py-2">SL</th>
                                    <th width="40%" class="py-2">Attachment Title</th>
                                    <th width="40%" class="py-2">Attach File (Max: 3MB)</th>
                                    <th class="py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $oldAttachments = old('attachments');
                                    if (!$oldAttachments) {
                                        $oldAttachments = $requisition->requAttachments->map(function ($a) {
                                            return [
                                                'title' => $a->attachment_title,
                                                'file' => $a->attachment,
                                            ];
                                        })->toArray();
                                    }
                                @endphp

                                @forelse($oldAttachments as $index => $attachment)
                                    <tr>
                                        <td class="sl"></td>
                                        <td>
                                            <input type="text" placeholder="Attachment Title" name="attachments[][title]" class="form-control form-control-sm" value="{{ $attachment['title'] }}">
                                            @error('attachments.' . $index . '.title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="file" name="attachments[][file]" class="form-control form-control-sm">
                                            @if (!empty($attachment['file']))
                                                <a href="{{ asset('public/attachments/' . $attachment['file']) }}" class="" target="_blank">View Existing File</a>
                                            @endif
                                            @error('attachments.' . $index . '.file')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm removeRowAttachment"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- JS will add one row if no old input -->
                                @endforelse

                            </tbody>

                        </table>
                        <button type="button" id="addRowAttachment" class="btn btn-success float-end btn-sm"><i class="fa fa-plus"></i> Add More</button>
                    </fieldset>
                </div>
                <div class="form-group col-12 mt-2">
                    <fieldset class="border scheduler-border">
                        <div class="scheduler-border">
                            <p class="float-none">Comments</p>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label for="">Specification</label>
                                <textarea name="specification" class="form-control" placeholder="Specification">{{ old('specification', $requisition->specification) }}</textarea>
                                @error('specification')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-4">
                                <label for="">Justification</label>
                                <textarea name="justification" class="form-control" placeholder="Justification">{{ old('justification', $requisition->justification) }}</textarea>
                                @error('justification')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-4">
                                <label for="">Remark</label>
                                <textarea name="remark" class="form-control" placeholder="Remark">{{ old('remark', $requisition->remark) }}</textarea>
                                @error('remark')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="form-group d-flex justify-content-center gap-2 float-end">
                    <button class="btn btn-success mt-3" type="submit" onclick="overlay('show');">Update</button>
                    <button type="button" class="btn btn-info gradient mt-3" onclick="cancel('/requisition/list/table-stationery')">Back</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden template row for JS cloning -->
<template id="templateRow">
    <tr>
        <td class="sl"></td>
        <td>
            <select name="product_ids[]" class="form-select form-select-sm product-dropdown">
                <option value="">Select Product</option>
                @foreach ($items as $item)
                    <option
                        value="{{ $item->id }}"
                        {{-- data-code="{{ $item->product_code }}" --}}
                        data-image="{{ $item->image }}"
                        data-name="{{ $item->product_name }}"
                        data-unit="{{ $item->unit->name }}"
                    >
                        {{ $item->product_name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="quantities[]" class="form-control form-control-sm" placeholder="Quantity" min="1" value="1">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>
        </td>
    </tr>
</template>


<table style="display: none;">
    <tbody id="templateRowAttachment">
        <tr>
            <td class="sl">1.</td>
            <td>
                <input type="text" name="attachments[][title]" class="form-control form-control-sm" placeholder="Attachment Title">
            </td>
            <td>
                <input type="file" name="attachments[][file]" id="" class="form-control form-control-sm" placeholder="Attach File">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeRowAttachment"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
    </tbody>
</table>
<div id="fullscreen-hover-preview" style="display: none;">
    <img src="" alt="Preview">
</div>
@endsection

@push('scripts')
<script>
    function updateProductDropdownOptions() {
        // Collect all selected values
        let selectedValues = $('.product-dropdown').map(function () {
            return $(this).val();
        }).get();

        // For each dropdown
        $('.product-dropdown').each(function () {
            let $select = $(this);
            let currentVal = $select.val();

            // Clear all options
            $select.empty();

            // Add default option
            $select.append('<option value="">Select Product</option>');

            // Add all options except those selected in other dropdowns
            @foreach ($items as $item)
                if (!selectedValues.includes('{{ $item->id }}') || currentVal === '{{ $item->id }}') {
                    $select.append(`<option
                        value="{{ $item->id }}"
                        data-image="{{ $item->image }}"
                        data-name="{{ $item->product_name }}"
                        data-unit="{{ $item->unit->name }}"
                    >{{ $item->product_name }}</option>`);
                }
            @endforeach

            // Set back its value and trigger update
            $select.val(currentVal).trigger('change.select2');
        });
    }

    function formatProduct(item) {
        if (!item.id) return item.text;

        const $item = $(item.element);
        const name = $item.data('name');
        const unit = $item.data('unit');
        const image = $item.data('image');
        const imageUrl = image ? "{{ asset('public/items') }}/" + image : "{{ asset('public/images/placeholder-image.png') }}";

        return $(`
            <div style="display: flex; font-size: 13px;">
                <div style="width: 20%;"><img src="${imageUrl}" width="20" class="hover-fullscreen-trigger" data-full="${imageUrl}"></div>
                <div style="width: 70%;">${name}</div>
                <div style="width: 30%; text-align: left;">${unit}</div>
            </div>
        `);
    }

    function updateSerialNumberForProducts() {
        $('#productTable tbody tr').each(function(index) {
            $(this).find('td.sl').text((index + 1) + '.');
            if (index === 0) {
                $(this).find('.removeRow').hide();
            } else {
                $(this).find('.removeRow').show();
            }
        });
    }

    $('#addRow').on('click', function () {
        let newRow = $('#templateRow').html();
        let $row = $(newRow);
        $('#productTable tbody').append($row);

        updateSerialNumberForProducts();

        // Initialize Select2 for the new select element
        let $dropdown = $row.find('.product-dropdown');

        $dropdown.removeClass('form-select-sm').select2({
            placeholder: 'Select Product',
            allowClear: true,
            width: '100%',
            templateResult: formatProduct,
            templateSelection: formatProduct,
        });

        // Bind change event
        $dropdown.on('change', function () {
            updateProductDropdownOptions();
        });

        // Trigger change to ensure updateProductDropdownOptions handles it
        $dropdown.trigger('change');
    });


    // Delegate remove button click
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
        updateSerialNumberForProducts();
        updateProductDropdownOptions();
    });

    // Initialize Select2 for after page load if validation fails
    function initializeSelect2() {
        let $dropdowns = $('.product-dropdown');
        $dropdowns.each(function () {
            $(this).select2({
                placeholder: 'Select Product',
                allowClear: true,
                width: '100%',
                templateResult: formatProduct,
                templateSelection: formatProduct,
            });

            $(this).on('change', function () {
                updateProductDropdownOptions();
            });

            $(this).trigger('change');
        });
    }

    // Add initial row on page load
    $(document).ready(function () {
        updateSerialNumberForProducts();
        var oldProductInput = @json($oldProducts);
        if (oldProductInput.length > 0) {
            initializeSelect2();
        }
    });
</script>

<script>
    function updateSerialNumberForAttachment() {
        $('#attachmentTable tbody tr').each(function(index) {
            $(this).find('td.sl').text((index + 1) + '.');
            if (index === 0) {
                $(this).find('.removeRowAttachment').hide();
            } else {
                $(this).find('.removeRowAttachment').show();
            }
        });
    }

    $('#addRowAttachment').on('click', function () {
        let newRow = $('#templateRowAttachment').html();
        $('#attachmentTable tbody').append(newRow);
        updateSerialNumberForAttachment();
    });

    // Delegate remove button click
    $(document).on('click', '.removeRowAttachment', function () {
        $(this).closest('tr').remove();
        updateSerialNumberForAttachment();
    });

    // Add initial row on page load
    $(document).ready(function () {
        if ($('#attachmentTable tbody tr').length === 0) {
            $('#addRowAttachment').click();
        }
        updateSerialNumberForAttachment();
    });
</script>
@endpush
