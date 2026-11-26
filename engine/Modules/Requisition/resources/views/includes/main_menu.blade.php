<style type="text/css">
    .navbar {
        padding-top: 2px;
        padding-bottom: 2px;
    }
    .navbar li .nav-link.active
    {
        color: #fff;
    }
    .navbar button {
        background-color: rgb(32, 178, 170);
        color: #ffffff;
    }
    .navbar-nav li a {
        padding: 7px 20px;
        color: #ffffff;
        background-color: #375eb1;
        border: 1px solid #333;
        font-size: 15px;
        margin: 2px 2px;
    }
    .navbar-nav li a:hover{
        color: #ffffff;
        background-color: #166834;
        border-color: #E2E2E2;
    }
    .active > a {
        border-style: solid;
        border-width: 1px;
        color: #ffffff;
        background-color: #166834 !important;
        border-color: #333 !important;
    }

     /* for dropdown menu styling  */
     .dropdown-menu .dropdown-item {
        padding: 7px 20px;
        color: #040404;
        background-color: #ffffff;
        border: 1px solid #333;
        font-size: 15px;
        margin: 2px 2px;
    }

    .dropdown-menu .dropdown-item:hover {
        color: #ffffff;
        background-color: #166834;
        border-color: #E2E2E2;
    }

    .dropdown-menu .active {
        background-color: #166834 !important;
        border-color: #333 !important;
        color: #ffffff;
    }
    .navbar .dropdown-toggle {
        color: #ffffff;
        background-color: #375eb1;
        border: 1px solid #333;
        padding: 7px 20px;
        font-size: 15px;
        margin: 2px 2px;
    }

    .navbar .dropdown-toggle:hover {
        background-color: #166834;
        border-color: #E2E2E2;
    }

    button.active {
        background-color: #166834 !important;
        border-color: #333 !important;
        color: #fff !important;
    }

</style>
<?php $viewFrom = (!empty($_GET['viewFrom'])) ? $_GET['viewFrom'] : ''; ?>
<nav class="navbar navbar-expand-lg">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-bars"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent" style="margin: 5px 0px">
        <ul class="navbar-nav mr-auto nav nav-pills">
            <li class="{{($controller == 'RequisitionDashboardController' && $action == 'dashboard') ? 'active' : '' }}">
                <a class="nav-link {{($controller == 'RequisitionDashboardController' && $action == 'dashboard') ? 'active' : '' }}" href="{{ route('requisition.dashboard') }}">
                    <i class="fa fa-file-text-o"></i> Dashboard
                </a>
            </li>
            @can('accessRequisitionManagement')
                <li class="{{($controller == 'RequisitionController' && $action == 'index') ? 'active' : '' }}">
                    <a class="nav-link {{($controller == 'RequisitionController' && $action == 'index') ? 'active' : '' }}" href="{{ route('requisition.index') }}">
                        <i class="fa fa-file-text-o"></i> Requisition Management
                    </a>
                </li>
            @endcan


            @php
                $role = Auth::user()->roles->pluck('name')->first();
                $groupName = Auth::user()->user_subgroupinfo->subgroup_name
            @endphp

            @if(($role == 'requisitionAdminMaker' || $role == 'requisitionSupervisor') && $groupName == 'Printing Stationery')

            <li class="{{($controller == 'StockController' && $action == 'list') ? 'active' : '' }}">
                <a class="nav-link {{($controller == 'StockController' && $action == 'list') ? 'active' : '' }}"
                href="{{ route('requisition.stock.list', ['type' => 'reqAdminMaker']) }}">
                    <i class="fa fa-file-text-o"></i> Stock Management
                </a>
            </li>

            @endif

            <li class="{{ ($controller == 'RequisitionReportController' && $action == 'index') ? 'active' : '' }}">
                <a class="nav-link {{($controller == 'RequisitionReportController' && $action == 'index') ? 'active' : '' }}" href="{{ route('requisition.report.index') }}">
                    <i class="fa fa-file-text-o"></i> Reports
                </a>
            </li>
        </ul>
    </div>
</nav>

