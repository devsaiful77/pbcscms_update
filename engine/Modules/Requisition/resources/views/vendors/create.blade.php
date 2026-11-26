@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('requisition.vendor.index') }}">Vendor List</a></li>
            <li class="breadcrumb-item active">Vendor Create</li>
        </ol>
    </nav>
</div>
<div class="card">
    <div class="card-body">
        <form action="{{ route('requisition.vendor.store') }}" method="POST">
            @csrf
            <div class="mt-3">
                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" placeholder="Name" class="form-control mb-2" value="{{ old('name') }}">
                    @error('name')
                    <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label">Vendor Email<span class="text-danger">*</span></label>
                    <input type="email" name="email" placeholder="Email" class="form-control mb-2" value="{{ old('email') }}">
                    @error('email')
                    <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label">Vendor Phone<span class="text-danger">*</span></label>
                    <input type="text" name="phone" placeholder="Phone" class="form-control mb-2" value="{{ old('phone') }}">
                    @error('phone')
                    <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label class="form-label">Vendor Address<span class="text-danger">*</span></label>
                    <input type="text" name="office_address" placeholder="Office Address" class="form-control mb-2" value="{{ old('office_address') }}">
                    @error('office_address')
                    <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mt-2 me-2">Save</button>
                <button type="button" class="btn btn-warning gradient mt-2" onclick="cancel('/requisition/vendor')">Back</button>
            </div>
        </form>
    </div>
</div>
@endsection
