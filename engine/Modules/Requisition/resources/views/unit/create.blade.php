@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item">Mangage Item</li>
            <li class="breadcrumb-item"><a href="{{ route('requisition.unit.index') }}">Unit</a></li>
            <li class="breadcrumb-item active">Add Unit</li>
        </ol>
    </nav>
</div>
<div class="mt-3">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisition.unit.store') }}" method="POST">
                @csrf
                <div class="col-4">
                    <label class="form-label">Unit Name</label>
                    <input type="text" name="name" placeholder="Name" class="form-control mb-2">
                    @error('name')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="btn btn-primary mt-2">Submit</button>
                    <button type="button" class="btn btn-info gradient mt-2" onclick="cancel('/general-service')">Back</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
