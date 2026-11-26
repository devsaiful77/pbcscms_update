@extends('layouts.admin')

@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item">Mangage Item</li>
            <li class="breadcrumb-item"><a href="{{ route('requisition.printing-stationery.index') }}">Printing Stationary Items</a></li>
            <li class="breadcrumb-item active">Edit Printing Stationary Items</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisition.printing-stationery.update', $item->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="product_name">Name <span class="required">*</span></label>
                    <input type="text" id="product_name" name="product_name" class="form-control mb-2"
                        value="{{ old('product_name', $item->product_name) }}" placeholder="Product Name">
                    @error('product_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="product_code">Code </label>
                    <input type="text" id="product_code" name="product_code" class="form-control mb-2"
                        value="{{ old('product_code', $item->product_code) }}" placeholder="Product Code">
                    @error('product_code')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="unit_id">Unit Type <span class="required">*</span></label>
                    <select name="unit_id" id="unit_id" class="form-select mb-2">
                        <option value="">Select Unit</option>
                        @foreach ($unitTypes as $unit)
                            <option value="{{ $unit->id }}"
                                {{ old('unit_type', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="unit_price">Unit Price</label>
                    <input type="number" id="unit_price" value="{{ old('unit_price', $item->unit_price) }}" class="form-control mb-2" name="unit_price" placeholder="Unit Price">
                    @error('unit_price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="image">Image (Max: 3MB)</label>
                    <input type="file" value="{{ old('image') }}" class="form-control mb-2" name="image" placeholder="Product Image">
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary mt-2">Update</button>
                <button type="button" class="btn btn-info gradient mt-2" onclick="cancel('/requisition/printing-stationeries')">Back</button>
            </form>
        </div>
    </div>
</div>
@endsection
