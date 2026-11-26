<?php

use Illuminate\Support\Facades\Route;
use Modules\Requisition\App\Http\Controllers\BothUsersController;
use Modules\Requisition\App\Http\Controllers\NonPrintingRequisitionController;
use Modules\Requisition\App\Http\Controllers\PrintingRequisitionController;
use Modules\Requisition\App\Http\Controllers\PrintingStationeryController;
use Modules\Requisition\App\Http\Controllers\ProductUnitController;
use Modules\Requisition\App\Http\Controllers\RegionController;
use Modules\Requisition\App\Http\Controllers\RequisitionController;
use Modules\Requisition\App\Http\Controllers\RequisitionDashboardController;
use Modules\Requisition\App\Http\Controllers\RequisitionItemController;
use Modules\Requisition\App\Http\Controllers\RequisitionReportController;
use Modules\Requisition\App\Http\Controllers\RequisitionSMSEmailsController;
use Modules\Requisition\App\Http\Controllers\RequRolesController;
use Modules\Requisition\App\Http\Controllers\RequUsersController;
use Modules\Requisition\App\Http\Controllers\SlaConfigController;
use Modules\Requisition\App\Http\Controllers\StockController;
use Modules\Requisition\App\Http\Controllers\SupportServiceController;
use Modules\Requisition\App\Http\Controllers\SupportServiceRequisitionController;
use Modules\Requisition\App\Http\Controllers\TableStationeryController;
use Modules\Requisition\App\Http\Controllers\VendorController;

Route::get('both-access', [BothUsersController::class, 'index']);
Route::get('service-access', [BothUsersController::class, 'service'])->name('redirect.service');
Route::get('requisiution-access', [BothUsersController::class, 'requisiution'])->name('redirect.requisition');


Route::get('/send-vendor-email', ['middleware' => [], 'uses' => 'SendVendorEmailController@sendVendorEmail']);

Route::prefix('requisition')->name('requisition.')->middleware(['module.access_requ'])->group(function () {
    Route::get('users', [RequUsersController::class, 'index'])->name('users');
    Route::get('users/add', [RequUsersController::class, 'add'])->name('user.add');
    Route::get('users/edit/{id}', [RequUsersController::class, 'edit']);
    Route::post('users/store', [RequUsersController::class, 'store'])->name('user.store');
    Route::post('users/edit/{id}', [RequUsersController::class, 'update'])->name('user.update');
    Route::get('users/action-queue-list', [RequUsersController::class, 'userTmpList'])->name('users.action.queue.list');

    Route::get('users/approve/{id}', ['middleware' => ['auth'], 'uses' => 'RequUsersController@approve']);
    Route::get('users/assign/{id}', ['middleware' => ['auth'], 'uses' => 'RequUsersController@userAssign']);
    Route::get('users/send-back/{id}', ['middleware' => ['auth'], 'uses' => 'RequUsersController@userSendback']);
    Route::get('users/reject/{id}', ['middleware' => ['auth'], 'uses' => 'RequUsersController@userReject']);
    Route::get('users/fetch/checker-table/{id}', ['middleware' => ['auth'], 'uses' => 'RequUsersController@checkerTableData']);
    Route::get('users/fetch/button-status/{id}', ['middleware' => ['auth'], 'uses' => 'RequUsersController@buttonControl']);

    Route::get('roles', [RequRolesController::class, 'index']);
    Route::get('rolesCreate', [RequRolesController::class, 'create']);
    Route::post('rolesStore', [RequRolesController::class, 'store']);
    Route::get('roleEdit/{id}', [RequRolesController::class, 'edit']);
    Route::patch('roleUpdate/{id}', [RequRolesController::class, 'update']);
    Route::get('roleDelete/{id}', [RequRolesController::class, 'destroy']);

    Route::get('/', [RequisitionDashboardController::class, 'index'])->name('requisition.index');
    Route::get('/dashboard', [RequisitionDashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/dashboard/filter', [RequisitionDashboardController::class, 'getFilteredData'])->name('dashboard.filter');
    Route::post('/dashboard/filter-by-product', [RequisitionDashboardController::class, 'getFilteredDataByProduct'])->name('dashboard.filter-by-product');
    Route::get('/dashboard/export', [RequisitionDashboardController::class, 'dashboardExport'])->name('dashboard.export');



    Route::resource('vendor', VendorController::class)->names('vendor');
    Route::get('vendor/{vendor}/{status}', [VendorController::class, 'status'])->name('vendor.status');
    Route::get('vendor/{vendor}', [VendorController::class, 'productsView'])->name('vendor.products');
    Route::post('vendor/vendor-product-store', [VendorController::class, 'productStore'])->name('vendor.product.store');
    Route::get('vendor/vendor-product-delete/{vendor}/{product}', [VendorController::class, 'deleteVendorProduct'])->name('vendor.product.delete');
    Route::get('vendor/product-validation/{vendor_id}/{product_id}', [VendorController::class, 'vendorProductValidation'])
        ->name('vendor.product.validation');

    Route::get('items', [RequisitionItemController::class, 'index'])->name('items');
    Route::get('items/categories', [RequisitionItemController::class, 'requisitionCategory'])->name('category');
    Route::get('items/get/sub-categories', [RequisitionItemController::class, 'getSubCategory'])->name('get.sub-category');
    Route::get('items/sub-categories', [RequisitionItemController::class, 'requisitionSubCategory'])->name('sub-category');
    Route::get('items/create', [RequisitionItemController::class, 'create'])->name('items.create');
    Route::post('items/post', [RequisitionItemController::class, 'store'])->name('items.store');
    Route::get('items/edit/{id}', [RequisitionItemController::class, 'edit'])->name('items.edit');
    Route::put('items/update/{id}', [RequisitionItemController::class, 'update'])->name('items.update');
    Route::get('items/status/{id}/{status}', [RequisitionItemController::class, 'status'])->name('items.status');

    Route::resource('unit', ProductUnitController::class)->names('unit');
    Route::get('unit/{unitservice}/{status}', [ProductUnitController::class, 'status'])->name('unit.status');

    Route::get('stock/list/{type?}', [StockController::class, 'list'])->name('stock.list');
    Route::get('stock/{type?}', [StockController::class, 'index'])->name('stock');
    Route::get('stock/history', [StockController::class, 'history'])->name('stock.history');
    Route::get('stock/quantity/{id}', [StockController::class, 'quantity'])->name('stock.quantity');
    Route::post('stock/store/{type?}', [StockController::class, 'quantityStore'])->name('stock.store');

    //general support service routes
    Route::get('general-support-services', [SupportServiceController::class, 'index'])->name('support-service.index');
    Route::get('general-support-services/create', [SupportServiceController::class, 'create'])->name('support-service.create');
    Route::post('general-support-services/store', [SupportServiceController::class, 'store'])->name('support-service.store');
    Route::get('general-support-services/edit/{id}', [SupportServiceController::class, 'edit'])->name('support-service.edit');
    Route::put('general-support-services/update/{id}', [SupportServiceController::class, 'update'])->name('support-service.update');
    Route::get('general-support-services/status/{id}/{status}', [SupportServiceController::class, 'status'])->name('support-service.status');

    //printing stationery routes
    Route::get('printing-stationeries', [PrintingStationeryController::class, 'index'])->name('printing-stationery.index');
    Route::get('printing-stationeries/create', [PrintingStationeryController::class, 'create'])->name('printing-stationery.create');
    Route::post('printing-stationeries/store', [PrintingStationeryController::class, 'store'])->name('printing-stationery.store');
    Route::get('printing-stationeries/edit/{id}', [PrintingStationeryController::class, 'edit'])->name('printing-stationery.edit');
    Route::put('printing-stationeries/update/{id}', [PrintingStationeryController::class, 'update'])->name('printing-stationery.update');
    Route::get('printing-stationeries/status/{id}/{status}', [PrintingStationeryController::class, 'status'])->name('printing-stationery.status');

    //table stationery routes
    Route::get('table-stationeries-item', [TableStationeryController::class, 'index'])->name('table-stationery-item.index');
    Route::get('table-stationeries-item/create', [TableStationeryController::class, 'create'])->name('table-stationery-item.create');
    Route::post('table-stationeries-item/store', [TableStationeryController::class, 'store'])->name('table-stationery-item.store');
    Route::get('table-stationeries-item/edit/{id}', [TableStationeryController::class, 'edit'])->name('table-stationery-item.edit');
    Route::put('table-stationeries-item/update/{id}', [TableStationeryController::class, 'update'])->name('table-stationery-item.update');
    Route::get('table-stationeries-item/status/{id}/{status}', [TableStationeryController::class, 'status'])->name('table-stationery-item.status');

    //requisition management routes
    Route::middleware('role_or_permission:accessRequisitionManagement')->group(function () {
        Route::get('list/create', [RequisitionController::class, 'create'])->name('create');
        Route::get('my-requisition-list', [RequisitionController::class, 'index'])->name('index');
        Route::get('handler-export', [RequisitionController::class, 'handlerExport'])->name('handler-export');

        Route::get('list/create-table-stationery', [NonPrintingRequisitionController::class, 'create'])->name('create-table-stationery');
        Route::post('list/create-table-stationery-store', [NonPrintingRequisitionController::class, 'nonPrintingItemStore'])->name('create-table-stationery.store');
        Route::post('list/printing-item-store', [PrintingRequisitionController::class, 'printingItemStore'])->name('printing-item-store');
        Route::get('list/create-printing-stationery', [PrintingRequisitionController::class, 'createPrintingStationery'])->name('create-printing-stationery');
        Route::get('list/create-support-service/{type}', [SupportServiceRequisitionController::class, 'createSupportService'])->name('create-support-service');
        Route::post('list/support-service-store', [SupportServiceRequisitionController::class, 'supportServiceStore'])->name('support-service-store');
    });

    Route::get('publish/{requisition_number}', [RequisitionController::class, 'publish'])->name('publish');
    Route::post('attachment-upload', [RequisitionController::class, 'attachUpload'])->name('single_attach_upload');
    Route::delete('attachment/delete/{id}', [RequisitionController::class, 'deleteAttachment'])->name('requisition.attachment.delete');



    Route::get('table-stationery/details/{requisition_number}', [NonPrintingRequisitionController::class, 'details'])->name('table-stationery.details');
    Route::get('table-stationery/history/{requisition_number}', [NonPrintingRequisitionController::class, 'history'])->name('table-stationery.history');
    Route::get('table-stationery/edit/{requisition_number}', [NonPrintingRequisitionController::class, 'edit'])->name('table-stationery.edit');
    Route::post('table-stationery-update/{id}', [NonPrintingRequisitionController::class, 'update'])->name('table-stationery.update');
    Route::get('table-stationery/vendor-details', [NonPrintingRequisitionController::class, 'getVendorDetails'])->name('table-stationery.vendor.details');
    Route::post('workingOnTableREQ', [NonPrintingRequisitionController::class, 'workingOnTableREQ'])->name('workingOnTableREQ');
    Route::get('table-stationery/assign/{requisition_number}', [NonPrintingRequisitionController::class, 'assign'])->name('table-stationery.assign');

    // printing requisition routes
    Route::post('printing-item-update/{id}', [PrintingRequisitionController::class, 'update'])->name('printing-item.update');
    Route::get('printing-item/details/{requisition_number}', [PrintingRequisitionController::class, 'details'])->name('printing-item.details');
    Route::get('printing-item/history/{requisition_number}', [PrintingRequisitionController::class, 'history'])->name('printing-item.history');
    Route::get('printing-item/edit/{requisition_number}', [PrintingRequisitionController::class, 'edit'])->name('printing-item.edit');
    Route::post('workingOnPrintREQ', [PrintingRequisitionController::class, 'workingOnPrintREQ'])->name('workingOnPrintREQ');
    Route::get('printing-item/assign/{requisition_number}', [PrintingRequisitionController::class, 'assign'])->name('printing.assign');

    Route::post('requisition/printing/quick-action', [PrintingRequisitionController::class, 'quickAction'])->name('printing.quickAction');

    // general support service routes
    Route::post('support-service-update/{id}', [SupportServiceRequisitionController::class, 'supportServiceUpdate'])->name('support-service-update');
    Route::get('general-service/details/{requisition_number}', [SupportServiceRequisitionController::class, 'details'])->name('general-service.details');
    Route::get('general-service/history/{requisition_number}', [SupportServiceRequisitionController::class, 'history'])->name('general-service.history');
    Route::get('general-service/edit/{requisition_number}', [SupportServiceRequisitionController::class, 'edit'])->name('general-service.edit');
    Route::post('general-service/workingOnREQ', [SupportServiceRequisitionController::class, 'workingOnREQ'])->name('general-service.workingOnREQ');
    Route::get('general-service/assign/{requisition_number}', [SupportServiceRequisitionController::class, 'assign'])->name('general-service.assign');
    Route::get('general-service/vendor-details', [SupportServiceRequisitionController::class, 'getVendorDetails'])->name('general-service.vendor.details');
    Route::get('general-service/vendor-mail-generate', [SupportServiceRequisitionController::class, 'vendorMailGenerate'])->name('general-service.vendor-mail-generate');
    // region routes
    Route::get('region/list', [RegionController::class, 'index'])->name('region.index');
    Route::get('region/create', [RegionController::class, 'create'])->name('region.create');
    Route::post('region/store', [RegionController::class, 'store'])->name('region.store');
    Route::get('region/edit/{id}', [RegionController::class, 'edit'])->name('region.edit');
    Route::put('region/update/{id}', [RegionController::class, 'update'])->name('region.update');
    Route::get('region/status/{id}/{status}', [RegionController::class, 'status'])->name('region.status');
    Route::get('region/view/{id}', [RegionController::class, 'view'])->name('region.view');

    // SLA Config Routes
    Route::prefix('sla')->name('sla.')->group(function () {
        Route::get('gss/index', [SlaConfigController::class, 'gss_index'])->name('gss.index');
        Route::get('pi/index', [SlaConfigController::class, 'pi_index'])->name('pi.index');
        Route::get('npi/index', [SlaConfigController::class, 'npi_index'])->name('npi.index');
        Route::post('store-update', [SlaConfigController::class, 'storeOrUpdate'])->name('store-update');
    });

    Route::prefix('report')->name('report.')->group(function () {
        Route::get('index', [RequisitionReportController::class, 'index'])->name('index');
    });


    Route::get('/sms-email', [RequisitionSMSEmailsController::class, 'index'])->name('sms-email.index');
    Route::post('/sms-emails/store/{id}', [RequisitionSMSEmailsController::class, 'store'])->name('sms-emails.store');
});
