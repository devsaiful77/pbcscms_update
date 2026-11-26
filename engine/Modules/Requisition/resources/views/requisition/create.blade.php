@php use Modules\Requisition\App\Constants\RequisitionConstants; @endphp
@extends('requisition::layouts.master')

@section('content')
<style>
    .request-box {
        min-width: 260px;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-size: 18px;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s ease;
        gap: 12px;
    }

    .request-box i {
        font-size: 38px;
    }

    .request-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.15);
    }
</style>

<div class="mt-5">
    
    <div class="d-flex justify-content-center mt-5 mb-4 gap-4 flex-wrap">
        <!-- Printing Stationery -->
        <a href="{{ route('requisition.create-printing-stationery') }}" 
           class="btn btn-outline-primary request-box"
           onclick="overlay('show');">
            <i class="fa fa-print"></i>
            <span>Printing Stationery</span>
        </a>

        <!-- Table Stationery -->
        <a href="{{ route('requisition.create-table-stationery') }}" 
           class="btn btn-outline-primary request-box"
           onclick="overlay('show');">
            <i class="fa fa-clipboard"></i>
            <span>Table Stationery</span>
        </a>
    </div>

    @if ($hasRegion)
    <div class="d-flex justify-content-center gap-4 flex-wrap">
        <!-- Repair & Maintenance -->
        <a href="{{ route('requisition.create-support-service', RequisitionConstants::REPAIR_MAINTENANCE) }}" 
           class="btn btn-outline-success request-box"
           onclick="overlay('show');">
            <i class="fa fa-wrench"></i>
            <span>Repair & Maintenance</span>
        </a>

        <!-- Replacement/New Asset -->
        <a href="{{ route('requisition.create-support-service', RequisitionConstants::NEW_ASSET) }}" 
           class="btn btn-outline-success request-box"
           onclick="overlay('show');">
            <i class="fa fa-archive"></i>
            <span>Replacement / New Asset</span>
        </a>
    </div>

    @endif

</div>
@endsection

