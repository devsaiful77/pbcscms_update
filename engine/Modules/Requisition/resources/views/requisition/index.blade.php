@extends('requisition::layouts.master')

@section('content')
<div class="mt-3">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">Requisition List</h3>
        </div>
        <div class="card-body">
            @include('requisition::requisition.list-menu')

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="supportService" role="tabpanel">
                    {{-- General Support Service --}}
                </div>
                <div class="tab-pane fade" id="printingStationery" role="tabpanel">
                    {{-- Printing Stationery --}}
                </div>
                <div class="tab-pane fade" id="tableStationery" role="tabpanel">
                    {{-- Table Stationery --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
