@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item">Mangage Item</li>
            <li class="breadcrumb-item"><a href="{{ route('requisition.support-service.index') }}">General Support Service Items</a></li>
            <li class="breadcrumb-item active">Edit General Support Service Items</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisition.support-service.update', $supportService->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="product_name">Name <span class="required">*</span></label>
                    <input type="text" id="product_name"
                           value="{{ old('product_name', $supportService->product_name) }}"
                           class="form-control mb-2"
                           name="product_name"
                           placeholder="Product Name">
                    @error('product_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label" for="sub_category_id">Category <span class="required">*</span></label>
                    <select name="sub_category_id" id="sub_category_id" class="form-select mb-2">
                        <option value="">Select Category</option>
                        @foreach ($supportServiceCategories as $item)
                            <option value="{{ $item->id }}"
                                {{ old('sub_category_id', $supportService->sub_category_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->sub_category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sub_category_id')
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
                <button type="submit" class="btn btn-success mt-2">Update</button>
                <button type="button" class="btn btn-info gradient mt-2" onclick="cancel('/requisition/general-support-services')">Back</button>
            </form>
        </div>
    </div>
</div>
@endsection
