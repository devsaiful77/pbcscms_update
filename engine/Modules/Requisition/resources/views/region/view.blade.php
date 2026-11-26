@extends('layouts.admin')
@section('content')
<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('requisition.region.index') }}">Region</a></li>
            <li class="breadcrumb-item active">Region view</li>
        </ol>
    </nav>
</div>
<div class="mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <h5 class="mb-0">Region Details</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Region Name:</label>
                <div class="fs-5 text-dark">{{ $region->name }}</div>
            </div>

            <div>
                <label class="form-label fw-semibold text-muted">Associated Subgroups:</label>
                <div class="row">
                    <div class="col-6">
                        <table class="table table-bordered table-condensed text-center">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Subgroup Name</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($region->subgroupInfo->count() > 0)
                                @foreach ($region->subgroupInfo as $key => $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr> <th class="vcenter text-center" colspan="3">No items found</th> </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('requisition.region.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
    </div>
</div>
@endsection
