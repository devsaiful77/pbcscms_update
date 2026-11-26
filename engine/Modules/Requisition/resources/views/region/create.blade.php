@extends('layouts.admin')

@section('content')
<div class="mt-3">
    <div class="bs-example mb-4 mt-3">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('requisition.region.index') }}">Region</a></li>
                <li class="breadcrumb-item active">Region Create</li>
            </ol>
        </nav>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisition.region.store') }}" method="post">
                @csrf
                <div class="form-group col-6">
                    <label for="name" class="form-label">Region Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" id="name" class="form-control" placeholder="Enter Region Name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-6">
                    <label for="subgroup_info_id" class="form-label">Subgroup <span class="text-danger">*</span></label>
                    <select name="subgroup_info_id[]" id="subgroup_info_id" class="form-select select2" multiple="multiple">
                        @foreach ($subgroups as $subgroup)
                            <option value="{{ $subgroup['id'] }}"
                                {{ in_array($subgroup['id'], old('subgroup_info_id', [])) ? 'selected' : '' }}>
                                {{ $subgroup['name'] }}
                            </option>
                        @endforeach
                    </select>

                    @error('subgroup_info_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-info gradient" onclick="cancel('/requisition/region/list')">Back</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#subgroup_info_id').select2({
            placeholder: 'Select Subgroup',
            allowClear: true
        });
    });
</script>
@endpush
