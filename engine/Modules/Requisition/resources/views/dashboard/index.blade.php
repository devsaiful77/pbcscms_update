@extends('requisition::layouts.master')

@push('stylesheets')
<style>
    .filter-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .stat-card {
        border-radius: 16px;
        border: none;
        transition: transform 0.2s;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .stat-card.active {
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #0d6efd;
    }
    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }

    .table-custom { border-collapse: separate; border-spacing: 0 8px; }
    .table-custom tbody tr {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .table-custom tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        cursor: pointer;
    }
    .table-custom td { padding: 12px 8px; border-top: none; vertical-align: middle; }
    .table-custom th { background-color: #f8f9fa; font-weight: 600; padding: 12px 8px; }

    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .status-badge {
        cursor: pointer;
        transition: all 0.2s;
    }
    .status-badge:hover {
        transform: scale(1.05);
        opacity: 0.8;
    }
    .status-badge.active {
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #0d6efd;
    }

    .clickable-card { cursor: pointer; transition: all 0.3s; }
    .clickable-card.active {
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #0d6efd, 0 10px 20px rgba(0,0,0,0.1);
    }

    .filter-indicator {
        background: #e9ecef;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .filter-indicator .remove-filter {
        cursor: pointer;
        font-weight: bold;
        margin-left: 5px;
        color: #dc3545;
    }
    .filter-indicator .remove-filter:hover { color: #a71d2a; }

    .btn-outline-primary-custom {
        background: transparent;
        border: 1px solid #3498db;
        color: #3498db;
        border-radius: 20px;
        padding: 5px 20px;
        transition: all 0.3s;
    }
    .btn-outline-primary-custom:hover {
        background: #3498db;
        color: white;
    }
    .card-header-custom {
        background: white;
        border-bottom: 2px solid #e9ecef;
        padding: 15px 20px;
    }
    .toast-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1050;
    }
</style>
@endpush

@section('content')

@php
    $userUnit = Auth::user()->user_unit ?? '';
    $userRegionList = $userUnit->region_id;
    $unitId = $userUnit->unit_id;
    $userSubgroupInfoId = $userUnit->subgroup_info_id;
    $branchUser = false;
    $initiator = false;

    if($userSubgroupInfoId){
        $subgroup = App\SubgroupInfo::find($userSubgroupInfoId);
        $group = $subgroup->group_info_id;
        if($group == 1){
            $branchUser = true;
        }
    }
    if($unitId){
        if($unitId == 25){
            $initiator = true;
        }
    }

    $array = explode(',', $unitId);
    if(in_array(25, $array)){
        $initiator = true;
    }
@endphp

@if($userRegionList || $branchUser || $initiator)

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header text-white text-center py-3" style="background-color:#6f42c1">
                    <h3 class="mb-0">
                        <i class="fa fa-home me-2"></i>
                        Welcome to Dashboard
                    </h3>
                </div>

                <div class="card-body text-center py-5">
                    <h4 class="text-dark mb-3">
                        Hello, {{ Auth::user()->name ?? 'User' }} 👋
                    </h4>

                    <p class="text-muted mb-4" style="font-size:16px;">
                        Welcome to the <strong>Requisition Management System</strong>.
                        Use the navigation menu to access your available modules and perform your daily tasks efficiently.
                    </p>

                    <hr>

                    <p class="mb-0 text-secondary">
                        Have a productive day!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@else
<div class="container-fluid p-3">
    <!-- Filter Section -->
    <div class="filter-card">
        <form method="GET" action="{{ route('requisition.dashboard') }}" id="dashboardForm" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1">Date From</label>
                <input type="text" name="date_from" class="form-control datepicker"
                       value="{{ request()->date_from ?: now()->subMonth()->format('d-m-Y') }}"
                       placeholder="dd-mm-yyyy" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1">Date To</label>
                <input type="text" name="date_to" class="form-control datepicker"
                       value="{{ request()->date_to ?: now()->format('d-m-Y') }}"
                       placeholder="dd-mm-yyyy" autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1">Items per page</label>
                <input type="number" name="qty" class="form-control" value="{{ request()->qty ?? 10 }}" min="1" max="100">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1">View Type</label>
                <select name="view_type" class="form-select" id="viewTypeSelect">
                    <option value="table" {{ (request()->view_type ?? 'table') == 'table' ? 'selected' : '' }}>📋 Table View</option>
                    <option value="chart" {{ request()->view_type == 'chart' ? 'selected' : '' }}>📊 Chart View</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa fa-filter"></i> Apply</button>
            </div>
        </form>

        <!-- Search & Export Row -->
        <div class="row mt-3">
            <div class="col-md-7">
                <div class="d-flex gap-2">
                    <input type="text" id="searchInput" class="form-control"
                           placeholder="🔍 Search by requisition no, logger, product..."
                           value="{{ request()->search }}">
                    <button type="button" id="searchBtn" class="btn btn-outline-primary-custom">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <button type="button" id="resetFiltersBtn" class="btn btn-outline-secondary">
                        <i class="fa fa-undo"></i> Reset
                    </button>
                </div>
            </div>
            <div class="col-md-5 text-end">
                <a href="{{ route('requisition.dashboard.export', request()->except('page')) }}" class="btn btn-warning" id="handlerExport">
                    <i class="fa fa-download"></i> Export Excel
                </a>
            </div>
        </div>

        <!-- Active Filters Indicator -->
        <div class="row mt-3" id="filterIndicator" style="display: none;">
            <div class="col-12">
                <div class="filter-indicator">
                    <i class="fa fa-filter"></i> Active Filter:
                    <span id="filterTypeText"></span>
                    <span id="filterStatusText"></span>
                    <span class="remove-filter" id="removeFilterBtn">✕ Remove</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row 1 (Clickable) -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card text-white bg-gradient-primary clickable-card" data-type="newAsset">
                <div class="card-body">
                    <h6 class="card-title mb-1">New Asset</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['newAsset'] }}</h2>
                    <small>Total Requisitions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-white bg-gradient-success clickable-card" data-type="repair">
                <div class="card-body">
                    <h6 class="card-title mb-1">Repair & Maint.</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['repair'] }}</h2>
                    <small>Total Requisitions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-white bg-gradient-info clickable-card" data-type="printingStationery">
                <div class="card-body">
                    <h6 class="card-title mb-1">Printing Stationery</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['printing'] }}</h2>
                    <small>{{ $totalPrintQty }} items requested</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-white bg-gradient-warning clickable-card" data-type="tableStationery">
                <div class="card-body">
                    <h6 class="card-title mb-1">Table Stationery</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['nonPrinting'] }}</h2>
                    <small>{{ $totalTableQty }} items requested</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row 2 (Clickable) -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm clickable-card" data-type="printingStationery">
                <div class="card-body text-center">
                    <i class="fa fa-print fa-2x text-primary mb-2"></i>
                    <h5 class="mb-1">Printing Stock</h5>
                    <h3 class="fw-bold text-primary mb-0">{{ number_format($totalStock) }}</h3>
                    <small class="text-muted">Current inventory</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm clickable-card" data-type="repair">
                <div class="card-body text-center">
                    <i class="fa fa-wrench fa-2x text-success mb-2"></i>
                    <h5 class="mb-1">Repair Requested</h5>
                    <h3 class="fw-bold text-success mb-0">{{ number_format($totalRepairQty) }}</h3>
                    <small class="text-muted">Total quantity</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm clickable-card" data-type="newAsset">
                <div class="card-body text-center">
                    <i class="fa fa-cubes fa-2x text-info mb-2"></i>
                    <h5 class="mb-1">New Asset Qty</h5>
                    <h3 class="fw-bold text-info mb-0">{{ number_format($totalAssetQty) }}</h3>
                    <small class="text-muted">Total quantity</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Charts Section (Clickable segments & badges) -->
    @if($viewType == 'chart')
    <div class="row g-4 mb-4">
        <!-- Printing Stationery Status Chart -->
        <div class="col-md-6 col-lg-3">
            <div class="chart-container">
                <div class="text-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="fa fa-print text-primary"></i> Printing Stationery</h6>
                    <small class="text-muted">Total: {{ $printingStatus['total'] }}</small>
                </div>
                <div id="printingStatusChart" style="height: 280px;"></div>
                <div class="status-stats mt-2 small text-center" id="printingStatusBadges">
                    <span class="badge bg-warning status-badge m-1" data-type="printingStationery" data-status="pending">P: {{ $printingStatus['pending'] }}</span>
                    <span class="badge bg-dark status-badge m-1" data-type="printingStationery" data-status="draft">D: {{ $printingStatus['draft'] }}</span>
                    <span class="badge bg-info status-badge m-1" data-type="printingStationery" data-status="wip">W: {{ $printingStatus['wip'] }}</span>
                    <span class="badge bg-secondary status-badge m-1" data-type="printingStationery" data-status="hold">H: {{ $printingStatus['hold'] }}</span>
                    <span class="badge bg-success status-badge m-1" data-type="printingStationery" data-status="approved">A: {{ $printingStatus['approved'] }}</span>
                    <span class="badge bg-teal status-badge m-1" data-type="printingStationery" data-status="closed">C: {{ $printingStatus['closed'] }}</span>
                    <span class="badge bg-danger status-badge m-1" data-type="printingStationery" data-status="cancelled">X: {{ $printingStatus['cancelled'] }}</span>
                </div>
            </div>
        </div>

        <!-- Table Stationery Status Chart -->
        <div class="col-md-6 col-lg-3">
            <div class="chart-container">
                <div class="text-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="fa fa-table text-success"></i> Table Stationery</h6>
                    <small class="text-muted">Total: {{ $tableStatus['total'] }}</small>
                </div>
                <div id="tableStatusChart" style="height: 280px;"></div>
                <div class="status-stats mt-2 small text-center" id="tableStatusBadges">
                    <span class="badge bg-warning status-badge m-1" data-type="tableStationery" data-status="pending">P: {{ $tableStatus['pending'] }}</span>
                    <span class="badge bg-dark status-badge m-1" data-type="tableStationery" data-status="draft">D: {{ $tableStatus['draft'] }}</span>
                    <span class="badge bg-info status-badge m-1" data-type="tableStationery" data-status="wip">W: {{ $tableStatus['wip'] }}</span>
                    <span class="badge bg-secondary status-badge m-1" data-type="tableStationery" data-status="hold">H: {{ $tableStatus['hold'] }}</span>
                    <span class="badge bg-success status-badge m-1" data-type="tableStationery" data-status="approved">A: {{ $tableStatus['approved'] }}</span>
                    <span class="badge bg-teal status-badge m-1" data-type="tableStationery" data-status="closed">C: {{ $tableStatus['closed'] }}</span>
                    <span class="badge bg-danger status-badge m-1" data-type="tableStationery" data-status="cancelled">X: {{ $tableStatus['cancelled'] }}</span>
                </div>
            </div>
        </div>

        <!-- Repair & Maintenance Status Chart -->
        <div class="col-md-6 col-lg-3">
            <div class="chart-container">
                <div class="text-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="fa fa-wrench text-danger"></i> Repair & Maintenance</h6>
                    <small class="text-muted">Total: {{ $repairStatus['total'] }}</small>
                </div>
                <div id="repairStatusChart" style="height: 280px;"></div>
                <div class="status-stats mt-2 small text-center" id="repairStatusBadges">
                    <span class="badge bg-warning status-badge m-1" data-type="repair" data-status="pending">P: {{ $repairStatus['pending'] }}</span>
                    <span class="badge bg-dark status-badge m-1" data-type="repair" data-status="draft">D: {{ $repairStatus['draft'] }}</span>
                    <span class="badge bg-info status-badge m-1" data-type="repair" data-status="wip">W: {{ $repairStatus['wip'] }}</span>
                    <span class="badge bg-secondary status-badge m-1" data-type="repair" data-status="hold">H: {{ $repairStatus['hold'] }}</span>
                    <span class="badge bg-success status-badge m-1" data-type="repair" data-status="approved">A: {{ $repairStatus['approved'] }}</span>
                    <span class="badge bg-teal status-badge m-1" data-type="repair" data-status="closed">C: {{ $repairStatus['closed'] }}</span>
                    <span class="badge bg-danger status-badge m-1" data-type="repair" data-status="cancelled">X: {{ $repairStatus['cancelled'] }}</span>
                </div>
            </div>
        </div>

        <!-- New Asset Status Chart -->
        <div class="col-md-6 col-lg-3">
            <div class="chart-container">
                <div class="text-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="fa fa-cube text-info"></i> New Asset</h6>
                    <small class="text-muted">Total: {{ $newAssetStatus['total'] }}</small>
                </div>
                <div id="newAssetStatusChart" style="height: 280px;"></div>
                <div class="status-stats mt-2 small text-center" id="newAssetStatusBadges">
                    <span class="badge bg-warning status-badge m-1" data-type="newAsset" data-status="pending">P: {{ $newAssetStatus['pending'] }}</span>
                    <span class="badge bg-dark status-badge m-1" data-type="newAsset" data-status="draft">D: {{ $newAssetStatus['draft'] }}</span>
                    <span class="badge bg-info status-badge m-1" data-type="newAsset" data-status="wip">W: {{ $newAssetStatus['wip'] }}</span>
                    <span class="badge bg-secondary status-badge m-1" data-type="newAsset" data-status="hold">H: {{ $newAssetStatus['hold'] }}</span>
                    <span class="badge bg-success status-badge m-1" data-type="newAsset" data-status="approved">A: {{ $newAssetStatus['approved'] }}</span>
                    <span class="badge bg-teal status-badge m-1" data-type="newAsset" data-status="closed">C: {{ $newAssetStatus['closed'] }}</span>
                    <span class="badge bg-danger status-badge m-1" data-type="newAsset" data-status="cancelled">X: {{ $newAssetStatus['cancelled'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="fa fa-chart-line text-primary"></i> Top 5 Products by Requisition Quantity
                    </h6>
                    <small class="text-muted">
                        <i class="fa fa-mouse-pointer"></i> Click on any bar or label to filter
                    </small>
                </div>
                <div id="productChart" style="height: 400px;"></div>
                <div class="text-center mt-2 small text-muted">
                    <i class="fa fa-info-circle"></i> Click on any product bar to see all requisitions containing that product
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Table Section -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header-custom rounded-top">
            <h5 class="mb-0 fw-bold">
                <i class="fa fa-table me-2"></i>Requisition Details
                <span id="recordCount" class="badge bg-secondary ms-2">{{ $requisitions->total() }} records</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-custom w-100">
                    <thead>
                        <tr>
                            <th width="5%">S/L</th>
                            <th width="15%">Requisition Number</th>
                            <th width="15%">Logger</th>
                            <th width="35%">Product Name</th>
                            <th width="15%">Requested Qty</th>
                            <th width="15%">Current Stock</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @include('requisition::dashboard._table_rows', ['requisitions' => $requisitions])
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center" id="paginationLinks">
                {{ $requisitions->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container"></div>

@endif

@endsection

@push('scripts')
<script>
    // Status code mapping
    const statusCodeMap = {
        'pending': 1,
        'draft': 2,
        'wip': 3,
        'hold': 4,
        'approved': 10,
        'closed': 11,
        'cancelled': 0
    };

    const statusLabels = {
        0: 'Cancelled', 1: 'Pending', 2: 'Draft', 3: 'WIP',
        4: 'Hold', 10: 'Approved', 11: 'Closed'
    };

    let currentFilterType = null;
    let currentFilterStatus = null;
    let currentProductFilter = null;
    let currentSearch = '';
    let currentDateFrom = $('input[name="date_from"]').val();
    let currentDateTo = $('input[name="date_to"]').val();
    let currentQty = $('input[name="qty"]').val();

    // Toast notification function
    function showToast(type, message) {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : (type === 'error' ? 'danger' : 'info')} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle')} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        $('.toast-container').append(toastHtml);
        const toast = new bootstrap.Toast($('.toast').last());
        toast.show();
        setTimeout(() => {
            $('.toast').first().remove();
        }, 3000);
    }

    // Load filtered data by type/status
    function loadFilteredData() {
        $.ajax({
            url: '{{ route("requisition.dashboard.filter") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                date_from: currentDateFrom,
                date_to: currentDateTo,
                filter_type: currentFilterType,
                filter_status: currentFilterStatus !== null ? statusCodeMap[currentFilterStatus] : '',
                search: currentSearch,
                qty: currentQty
            },
            beforeSend: function() {
                $('#tableBody').html('<tr><td colspan="6" class="text-center py-5"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
            },
            success: function(response) {
                $('#tableBody').html(response.html);
                $('#recordCount').text(response.total + ' records');
                $('#paginationLinks').hide();

                $('.clickable-card').removeClass('active');
                $('.status-badge').removeClass('active');

                if (currentFilterType) {
                    $(`.clickable-card[data-type="${currentFilterType}"]`).addClass('active');
                    if (currentFilterStatus) {
                        $(`.status-badge[data-type="${currentFilterType}"][data-status="${currentFilterStatus}"]`).addClass('active');
                        $('#filterStatusText').html(` & ${statusLabels[statusCodeMap[currentFilterStatus]]}`);
                    } else {
                        $('#filterStatusText').html('');
                    }
                    $('#filterTypeText').html(currentFilterType.replace(/([A-Z])/g, ' $1').trim());
                    $('#filterIndicator').show();
                } else if (currentProductFilter) {
                    $('#filterTypeText').html(`Product: "${currentProductFilter}"`);
                    $('#filterStatusText').html('');
                    $('#filterIndicator').show();
                } else {
                    $('#filterIndicator').hide();
                }
            },
            error: function() {
                $('#tableBody').html('<tr><td colspan="6" class="text-center text-danger py-5">Error loading data. Please try again.</td></tr>');
                showToast('error', 'Error loading data');
            }
        });
    }

    // Load filtered data by product
    function filterByProduct(productName) {
        currentFilterType = null;
        currentFilterStatus = null;
        currentProductFilter = productName;

        $.ajax({
            url: '{{ route("requisition.dashboard.filter-by-product") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                date_from: currentDateFrom,
                date_to: currentDateTo,
                product_name: productName,
                search: currentSearch,
                qty: currentQty
            },
            beforeSend: function() {
                $('#tableBody').html('<tr><td colspan="6" class="text-center py-5"><i class="fa fa-spinner fa-spin"></i> Loading product data...</td></tr>');
            },
            success: function(response) {
                $('#tableBody').html(response.html);
                $('#recordCount').text(response.total + ' records');
                $('#paginationLinks').hide();

                $('.clickable-card').removeClass('active');
                $('.status-badge').removeClass('active');

                $('#filterTypeText').html(`Product: "${response.product_name}"`);
                $('#filterStatusText').html('');
                $('#filterIndicator').show();

                if (response.total > 0) {
                    showToast('success', `Found ${response.total} requisition items for "${response.product_name}"`);
                } else {
                    showToast('info', `No requisitions found for "${response.product_name}"`);
                }
            },
            error: function() {
                $('#tableBody').html('<tr><td colspan="6" class="text-center text-danger py-5">Error loading product data. Please try again.</td></tr>');
                showToast('error', 'Error loading product data');
            }
        });
    }

    // Click handlers for cards
    $('.clickable-card').on('click', function() {
        const type = $(this).data('type');
        currentProductFilter = null;
        if (currentFilterType === type && currentFilterStatus === null) {
            currentFilterType = null;
        } else {
            currentFilterType = type;
            currentFilterStatus = null;
        }
        loadFilteredData();
    });

    // Click handlers for status badges
    $('.status-badge').on('click', function(e) {
        e.stopPropagation();
        const type = $(this).data('type');
        const status = $(this).data('status');
        currentProductFilter = null;
        if (currentFilterType === type && currentFilterStatus === status) {
            currentFilterType = null;
            currentFilterStatus = null;
        } else {
            currentFilterType = type;
            currentFilterStatus = status;
        }
        loadFilteredData();
    });

    // Search functionality
    $('#searchBtn').on('click', function() {
        currentSearch = $('#searchInput').val();
        loadFilteredData();
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            currentSearch = $(this).val();
            loadFilteredData();
        }
    });

    // Remove filter
    $('#removeFilterBtn').on('click', function() {
        currentFilterType = null;
        currentFilterStatus = null;
        currentProductFilter = null;
        loadFilteredData();
    });

    // Reset all filters
    $('#resetFiltersBtn').on('click', function() {
        currentFilterType = null;
        currentFilterStatus = null;
        currentProductFilter = null;
        currentSearch = '';
        $('#searchInput').val('');
        loadFilteredData();
    });

    $(document).ready(function() {
        $(".datepicker").flatpickr({
            dateFormat: "d-m-Y",
            allowInput: true,
            maxDate: "today",
            onChange: function() {
                currentDateFrom = $('input[name="date_from"]').val();
                currentDateTo = $('input[name="date_to"]').val();
                loadFilteredData();
            }
        });

        $('#viewTypeSelect').on('change', function() {
            $('#dashboardForm').submit();
        });

        $('input[name="qty"]').on('change', function() {
            currentQty = $(this).val();
            loadFilteredData();
        });
    });

    // Chart rendering
    @if($viewType == 'chart')
    function createDonutChart(containerId, data, clickCallback) {
        const filteredData = data.filter(d => d.y > 0);
        if (filteredData.length === 0) {
            $('#' + containerId).html('<div class="text-center text-muted py-5">No data available</div>');
            return;
        }

        Highcharts.chart(containerId, {
            chart: { type: 'pie', backgroundColor: 'transparent', options3d: { enabled: true, alpha: 45 } },
            title: { text: '' },
            tooltip: { pointFormat: '{point.name}: <b>{point.y}</b> ({point.percentage:.1f}%)' },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    depth: 35,
                    dataLabels: { enabled: true, format: '{point.percentage:.0f}%', style: { fontSize: '10px' }, distance: 20 },
                    showInLegend: false,
                    size: '70%',
                    innerSize: '40%',
                    point: {
                        events: {
                            click: function() {
                                if (clickCallback) clickCallback(this.name.toLowerCase());
                            }
                        }
                    }
                }
            },
            series: [{ name: 'Requisitions', data: filteredData }]
        });
    }

    createDonutChart('printingStatusChart', [
        { name: 'Pending', y: {{ $printingStatus['pending'] }}, color: '#ffc107' },
        { name: 'Draft', y: {{ $printingStatus['draft'] }}, color: '#6c757d' },
        { name: 'WIP', y: {{ $printingStatus['wip'] }}, color: '#17a2b8' },
        { name: 'Hold', y: {{ $printingStatus['hold'] }}, color: '#fd7e14' },
        { name: 'Approved', y: {{ $printingStatus['approved'] }}, color: '#28a745' },
        { name: 'Closed', y: {{ $printingStatus['closed'] }}, color: '#20c997' },
        { name: 'Cancelled', y: {{ $printingStatus['cancelled'] }}, color: '#dc3545' }
    ], function(statusName) {
        currentFilterType = 'printingStationery';
        currentFilterStatus = statusName;
        currentProductFilter = null;
        loadFilteredData();
    });

    createDonutChart('tableStatusChart', [
        { name: 'Pending', y: {{ $tableStatus['pending'] }}, color: '#ffc107' },
        { name: 'Draft', y: {{ $tableStatus['draft'] }}, color: '#6c757d' },
        { name: 'WIP', y: {{ $tableStatus['wip'] }}, color: '#17a2b8' },
        { name: 'Hold', y: {{ $tableStatus['hold'] }}, color: '#fd7e14' },
        { name: 'Approved', y: {{ $tableStatus['approved'] }}, color: '#28a745' },
        { name: 'Closed', y: {{ $tableStatus['closed'] }}, color: '#20c997' },
        { name: 'Cancelled', y: {{ $tableStatus['cancelled'] }}, color: '#dc3545' }
    ], function(statusName) {
        currentFilterType = 'tableStationery';
        currentFilterStatus = statusName;
        currentProductFilter = null;
        loadFilteredData();
    });

    createDonutChart('repairStatusChart', [
        { name: 'Pending', y: {{ $repairStatus['pending'] }}, color: '#ffc107' },
        { name: 'Draft', y: {{ $repairStatus['draft'] }}, color: '#6c757d' },
        { name: 'WIP', y: {{ $repairStatus['wip'] }}, color: '#17a2b8' },
        { name: 'Hold', y: {{ $repairStatus['hold'] }}, color: '#fd7e14' },
        { name: 'Approved', y: {{ $repairStatus['approved'] }}, color: '#28a745' },
        { name: 'Closed', y: {{ $repairStatus['closed'] }}, color: '#20c997' },
        { name: 'Cancelled', y: {{ $repairStatus['cancelled'] }}, color: '#dc3545' }
    ], function(statusName) {
        currentFilterType = 'repair';
        currentFilterStatus = statusName;
        currentProductFilter = null;
        loadFilteredData();
    });

    createDonutChart('newAssetStatusChart', [
        { name: 'Pending', y: {{ $newAssetStatus['pending'] }}, color: '#ffc107' },
        { name: 'Draft', y: {{ $newAssetStatus['draft'] }}, color: '#6c757d' },
        { name: 'WIP', y: {{ $newAssetStatus['wip'] }}, color: '#17a2b8' },
        { name: 'Hold', y: {{ $newAssetStatus['hold'] }}, color: '#fd7e14' },
        { name: 'Approved', y: {{ $newAssetStatus['approved'] }}, color: '#28a745' },
        { name: 'Closed', y: {{ $newAssetStatus['closed'] }}, color: '#20c997' },
        { name: 'Cancelled', y: {{ $newAssetStatus['cancelled'] }}, color: '#dc3545' }
    ], function(statusName) {
        currentFilterType = 'newAsset';
        currentFilterStatus = statusName;
        currentProductFilter = null;
        loadFilteredData();
    });

    // Top Products Chart
    @if($chartData->count() > 0)
        const chartData = @json($chartData);
        const productNames = Object.keys(chartData);
        const productValues = Object.values(chartData);

        Highcharts.chart('productChart', {
            chart: {
                type: 'column',
                backgroundColor: 'transparent',
                borderRadius: 12
            },
            title: { text: '' },
            xAxis: {
                categories: productNames,
                title: { text: 'Product Name' },
                labels: {
                    rotation: -45,
                    style: { fontSize: '11px', cursor: 'pointer' },
                    events: {
                        click: function() {
                            filterByProduct(this.value);
                        }
                    }
                }
            },
            yAxis: {
                title: { text: 'Total Quantity' },
                min: 0,
                gridLineDashStyle: 'Dash'
            },
            tooltip: {
                valueSuffix: ' units',
                valueDecimals: 0
            },
            plotOptions: {
                column: {
                    borderRadius: 8,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}',
                        style: { fontWeight: 'bold' }
                    },
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                filterByProduct(this.category);
                            }
                        }
                    }
                }
            },
            colors: ['#4facfe'],
            series: [{
                name: 'Requested Qty',
                data: productValues,
                color: '#4facfe'
            }],
            credits: { enabled: false }
        });
    @endif
    @endif
</script>
@endpush
