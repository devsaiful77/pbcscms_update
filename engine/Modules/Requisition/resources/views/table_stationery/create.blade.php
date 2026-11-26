@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item">Mangage Item</li>
            <li class="breadcrumb-item"><a href="{{ route('requisition.table-stationery-item.index') }}">Table Stationary Items</a></li>
            <li class="breadcrumb-item active">Add Table Stationary</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisition.table-stationery-item.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="product_name">Name <span class="required">*</span></label>
                    <input type="text" id="product_name" value="{{ old('product_name') }}" class="form-control mb-2" name="product_name" placeholder="Product Name" required>
                    @error('product_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="unit_type">Unit Type <span class="required">*</span></label>
                    <select name="unit_id" id="unit_type" class="form-select mb-2" required>
                        <option value="">Select Unit</option>
                        @foreach ($unitTypes as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="unit_price">Unit Price</label>
                    <input type="number" id="unit_price" value="{{ old('unit_price') }}" class="form-control mb-2" name="unit_price" placeholder="Unit Price">
                    @error('unit_price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="image">Image (Max: 3MB)</label>
                    <input type="file" class="form-control mb-2" name="image" placeholder="Product Image">
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success mt-2">Submit</button>
                <button type="button" class="btn btn-info gradient mt-2" onclick="cancel('/requisition/table-stationeries-item')">Back</button>
            </form>
        </div>
    </div>
</div>
@endsection
