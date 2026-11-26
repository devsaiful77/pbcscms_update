@extends('layouts.admin')
@section('content')
<style>
    .select2-container {
        width: 100% !important;
    }
    /* Match Bootstrap form-control height */
    .select2-container .select2-selection--single {
        border: 1px solid #000 !important;
        height: 35px !important;
        padding: 5px 0px !important;
    }

    /* Center the text vertically */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
    }

    /* Adjust the arrow alignment */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 35px !important;
    }

    /* Apply 1px solid black border to all form controls */
    .form-control,
    .form-select {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    /* Arrow when closed (pointing down) */
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-style: solid !important;
        border-color: #000 transparent transparent transparent !important;
    }

    /* Arrow when open (pointing up) */
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-style: solid !important;
        border-color: transparent transparent #000 transparent !important;
    }

    /* For multi-select */
    .select2-container .select2-selection--multiple {
        border: 1px solid #000 !important;
        min-height: 35px !important;
        padding: 3px !important;
    }

    /* Fix the height of selected tags */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin-top: 7px !important;
    }

    /* Make the input inside multi-select aligned */
    .select2-container--default .select2-selection--multiple .select2-search__field {
        height: 18px !important;
        margin-top: 6px !important;
    }



</style>
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item active">Vendor Products</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            Vendor : {{ $vendor->name ?? "" }}
        </h3>
        <form action="{{ route('requisition.vendor.product.store') }}" method="post">
            @csrf
            <div class="d-flex align-items-center gap-2">
                <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                <div style="width: 450px;">
                    <select name="product_id" class="form-select select2">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-info" style="width: 150px;"><i class="fa fa-plus-circle"></i>  Add</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class ="{{ !empty($vendorProducts) ? 'commonDataTableAllAsc' : '' }} table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="vcenter text-white text-center requ-th-color">#</th>
                            <th class="vcenter text-center text-white requ-th-color">Product Name</th>
                            <th class="vcenter text-center text-white requ-th-color">Action</th>
                        </tr>
                    </thead>
                    <tbody style="word-break: break-all;">
                        @if(!empty($vendorProducts))
                        @foreach($vendorProducts as $key => $data)
                            <tr>
                                <td class="vcenter text-center">{{ $key+1 }}</td>
                                <td class="vcenter text-center"> {{ $data['product_name'] }} </td>
                                {{-- Delete Button --}}
                                <td class="vcenter actions text-center">
                                    <button 
                                        type="button"
                                        class="btn btn-danger btn-sm deleteBtn"
                                        data-url="{{ route('requisition.vendor.product.delete', [
                                            'vendor' => $vendor->id,
                                            'product' => $data['id']
                                        ]) }}"
                                        data-product="{{ $data['product_name'] }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        style="width: 150px;"
                                    >
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @else <tr> <td class="vcenter text-center" colspan="3"> <strong>Data Not Available</strong></td> </tr>
                        @endif
                    </tbody>
                </table>
                <a href="{{ route('requisition.vendor.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back To Vendor List</a>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete 
                <strong id="productName"></strong> ?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <a href="" id="confirmDeleteBtn" class="btn btn-danger">
                    Delete
                </a>
            </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        $('.deleteBtn').on('click', function () {

            let url = $(this).data('url');
            let product = $(this).data('product');

            $('#confirmDeleteBtn').attr('href', url);
            $('#productName').text(product);

        });

    });
</script>
@endsection
