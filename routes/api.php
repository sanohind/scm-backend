<?php

use App\Service\User\UserCreateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\TestMailController;
use App\Http\Controllers\Api\PrintController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\SynchronizeController;
use App\Http\Controllers\Api\ForecastController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ListingReportController;
use App\Http\Controllers\SynchronizeManualController;
use App\Http\Controllers\Api\Subcontractor\SubcontController;
use App\Http\Controllers\EmailNotificationSupplierController;
use App\Http\Controllers\Api\DeliveryNote\DN_DetailController;
use App\Http\Controllers\Api\DeliveryNote\DN_HeaderController;
use App\Http\Controllers\Api\PurchaseOrder\PO_DetailController;
use App\Http\Controllers\Api\PurchaseOrder\PO_HeaderController;
use Laravel\Sanctum\PersonalAccessToken;

// Route Login
Route::post('/login', [AuthController::class, 'login']);

// move email
Route::get('/move',[UserController::class,'moveEmail']);

// route view print
Route::get('/pohview/{po_no}', [PrintController::class, 'poHeaderView']);

// print dn
Route::get('/dnhview/{no_dn}', [PrintController::class, 'dnHeaderView']);
Route::get('/dnhviewconf/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
Route::get('/dnhviewout/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);

// print label
Route::get('/lbview/{no_dn}', [PrintController::class, 'labelAllView']);
Route::get('/dnout/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
Route::get('/dnqty/{no_dn}', [PrintController::class, 'labelQtyConfirm']);

// Route sync
Route::get('/mail-notification', [EmailNotificationSupplierController::class,'mail']);
Route::get('/sync', [SynchronizeController::class, 'sync']);
Route::get('/copyBusinessPartner', [SynchronizeController::class, 'copyBusinessPartner']);
Route::get('/copyPoHeader', [SynchronizeController::class, 'copyPoHeader']);
Route::get('/copyPoDetail', [SynchronizeController::class, 'copyPoDetail']);
Route::get('/copyDnHeader', [SynchronizeController::class, 'copyDnHeader']);
Route::get('/copyDnDetail', [SynchronizeController::class, 'copyDnDetail']);

// Route Super Admin
Route::middleware(['auth:sanctum','userRole:1'])->prefix('super-admin')->group(function () {

    // Route for show list of user
    Route::get('partner/list', [PartnerController::class, 'index']);

    // Route for show online user
    Route::get('dashboard', [DashboardController::class, 'dashboard']);

    // Route for detail active user
    Route::get('user/online', [DashboardController::class, 'detailActiveUser']);

    // Route for logout current useer active token
    Route::post('user/logout', [DashboardController::class, 'logoutByTokenId']);

    // Route for monthly login data
    Route::get('user/monthly', [DashboardController::class, 'monthlyLoginData']);

    /**
     * Route For User
     */
    // Route for get record data
    Route::get('user/index', [UserController::class, 'index']);
    // Route for get email user
    Route::get('user/email/{bp_code}', [UserController::class,'userEmail']);
    // Route for create user
    Route::post('user/store',[UserController::class, "store"]);
    // Route for edit user form
    Route::get('user/edit/{user}',[UserController::class, "edit"]);
    // Route for update user data
    Route::put('user/update/{user}',[UserController::class, "update"]);
    // Route for update status user
    Route::put('user/update/status/{user}',[UserController::class, "updateStatus"]);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Admin Purchasing
Route::middleware(['auth:sanctum','userRole:2'])->prefix('admin-purchasing')->group(function () {
    // Route sync data
    Route::get('sync', [SynchronizeManualController::class, 'syncManual']);

    // Route get partner list
    Route::get('partner/list', [PartnerController::class, 'index']);

    /**
     *  Route for Purchase Order
     *  @param $sp_code / supplier_code is bp_code
     */
    // Routefor get record po with specific user
    Route::get('po/index/{bp_code}',[PO_HeaderController::class, "index"]);
    // Route for show PO Detail list
    Route::get('po/detail/{po_no}',[PO_DetailController::class, "index"]);
    // Route for print PO
    Route::get('po/print/{po_no}', [PrintController::class, 'poHeaderView']);
    // Route for show PO history list
    Route::get('po/history/{bp_code}', [HistoryController::class, 'poHeaderHistory']);

    /**
     * Route for Performance Report
     */
    // Route for show list of performance report
    Route::get('performance-report/index/{bp_code}',[ListingReportController::class, "index"])->name('index');
    // Route for download performance report
    Route::get('performance-report/file/{filename}', [ListingReportController::class, 'getFile']);
    // Route for store Listing Report
    Route::post('performance-report/store',[ListingReportController::class, "store"]);

    /**
     * Route for Forecast
     */
    // Route for get record forecast with spesific user
    Route::get('forecast/index/{bp_code}', [ForecastController::class,"indexPurchasing"]);
    // Route for store forecast file
    Route::post('forecast/store', [ForecastController::class,"store"]);
    // Route for download forecast file
    Route::get('forecast/file/{filename}', [ForecastController::class,"getFile"]);
    // Route for delete forecast file
    Route::delete('forecast/delete/{forecast}', [ForecastController::class,"destroy"]);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Admin Warehouse
Route::middleware(['auth:sanctum','userRole:3'])->prefix('admin-warehouse')->group(function () {
    // Route sync data
    Route::get('sync', [SynchronizeManualController::class, 'syncManual']);

    // Route get partner data
    Route::get('partner/list', [PartnerController::class, 'index']);

    /**
     * Route for Delivery Note
     */
    // Route for get record DN with specific user
    Route::get('dn/index/{sp_code}',[DN_HeaderController::class, "indexWarehouse"]);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // Route fo prin DN label / kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);
    // Route fo get DN history
    Route::get('dn/history/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Admin Subcont
Route::middleware(['auth:sanctum', 'userRole:4'])->prefix('admin-subcont')->group(function () {
    // Route for get partner list
    Route::get('partner/list', [PartnerController::class, 'index']);

    /**
     * Route for Subcontractor
     */
    // Route for get list item Erp
    Route::get('item/list/item', [SubcontController::class,'getListItemErp']);
    // Route for get list item user
    Route::get('item/list/{bp_code}', [SubcontController::class,'getListItem']);
    // Route for admin get all list item user based on bp_code
    Route::get('item/all-list/{bp_code}',[SubcontController::class,'adminGetAllItem']);
    // Route for get index subcont item (include stock)
    Route::get('item/index/{bp_code}', [SubcontController::class,'indexItem']);
    // Route for store subcont item
    Route::post('item/store', [SubcontController::class,'createItem']);
    // Route for update subcont item
    Route::patch('item/update', [SubcontController::class,'updateItem']);
    // Route for delete subcont item
    Route::delete('item/delete', [SubcontController::class,'deleteItem']);
    // Route for get index subcont transaction
    Route::get('transaction/index/{bp_code}/{start_date}/{end_date}', [SubcontController::class,'indexTrans']);

    /**
     * Route for Delivery Note
     */
    // Route for get record DN with specific user
    Route::get('dn/index/{sp_code}',[DN_HeaderController::class, "indexWarehouse"]);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // Route fo prin DN label / kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);
    // Route fo get DN history
    Route::get('dn/history/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Supplier Marketing
Route::middleware(['auth:sanctum','userRole:5'])->prefix('supplier-marketing')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Graph PO And DN Yearly Data
    Route::get('yearData', [DashboardController::class, 'getYearlyData']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     *  Route for Purchase Order
     */
    // Route for show list PO Header
    Route::get('po/index',[PO_HeaderController::class, "index"]);
    // Route for show list PO Detail
    Route::get('po/detail/{po_no}',[PO_DetailController::class, "index"]);
    // Route for update list PO Header
    Route::put('po/update/{po_no}',[PO_HeaderController::class, "update"]);
    // route view PO history
    Route::get('po/history', [HistoryController::class, 'poHeaderHistory']);
    // route view print PO file
    Route::get('po/print/{po_no}', [PrintController::class, 'poHeaderView']);

    /**
     *  Route for Delivery Note
     */
    // Route for show list DN Header
    Route::get('dn/index',[DN_HeaderController::class, "index"]);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}',[DN_DetailController::class, "edit"]);
    // Route for update list DN Detail
    Route::put('dn/update',[DN_DetailController::class, "update"]);
    // route view DN history
    Route::get('dn/history', [HistoryController::class, 'dnHeaderHistory']);
    // route view print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // route view print DN label/ kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);

    /**
     *  Route for Performance Repot
     */
    // Route for show list Perfromance Report
    Route::get('performance-report/index',[ListingReportController::class, "index"]);
    // Route for download Performance Report
    Route::get('performance-report/file/{filename}', [ListingReportController::class, 'getFile']);

    /**
     * Route for Forecast
     */
    // Route for show list Forecast
    Route::get('forecast/index',[ForecastController::class,'indexSupplier']);
    // Route for download Forecast
    Route::get('forecast/file/{filename}', [ForecastController::class,"getFile"]);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Supplier Subcont Marketing
Route::middleware(['auth:sanctum','userRole:6'])->prefix('supplier-subcont-marketing')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Graph PO And DN Yearly Data
    Route::get('yearData', [DashboardController::class, 'getYearlyData']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     *  Route for Purchase Order
     */

    // Route for show list PO Header
    Route::get('po/index',[PO_HeaderController::class, "index"]);
    // Route for show list PO Detail
    Route::get('po/detail/{po_no}',[PO_DetailController::class, "index"]);
    // Route for update list PO Header
    Route::put('po/update/{po_no}',[PO_HeaderController::class, "update"]);
    // Route for PO history
    Route::get('po/history', [HistoryController::class, 'poHeaderHistory']);
    // Route for print PO
    Route::get('po/print/{po_no}', [PrintController::class, 'poHeaderView']);

    /**
     *  Route for Delivery Note
     */
    // Route for show list DN Header
    Route::get('dn/index',[DN_HeaderController::class, "index"]);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}',[DN_DetailController::class, "edit"]);
    // Route for update list DN Detail
    Route::put('dn/update',[DN_DetailController::class, "update"]);
    // Route for print DN
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // Route for print DN label / kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);
    // route view DN history
    Route::get('dn/history', [HistoryController::class, 'dnHeaderHistory']);

    /**
     *  Route for Performance Report
     */
    // Route for show list of Performance Report
    Route::get('performance-report/index',[ListingReportController::class, "index"]);
    // Route for download Performance Report
    Route::get('performance-report/file/{filename}', [ListingReportController::class, 'getFile']);

    /**
     *  Route for Subcontractor
     */
    // Route for get list subcont item
    Route::get('item/list', [SubcontController::class,'getListItem']);
    // Route for get index subcont item (include stock)
    Route::get('item/index/{param?}', [SubcontController::class,'indexItem']);
    // Route for get index subcont transaction
    Route::get('transaction/index', [SubcontController::class,'indexTrans']);
    // Route for store subcont transaction
    Route::post('transaction/store', [SubcontController::class,'createTransaction']);

    /**
     *  Route for Forcast
     */
    // Route for get list Forecast
    Route::get('forecast/index',[ForecastController::class,'indexSupplier']);
    // Route for download Forecast
    Route::get('forecast/file/{filename}', [ForecastController::class,"getFile"]);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Supplier Warehouse
Route::middleware(['auth:sanctum', 'userRole:7'])->prefix('supplier-warehouse')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Graph PO And DN Yearly Data
    Route::get('yearData', [DashboardController::class, 'getYearlyData']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     * Route for Delivery Note
     */
    // Route for get record DN with specific user
    Route::get('dn/index',[DN_HeaderController::class, "index"]);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for update list DN Detail
    Route::put('dn/update',[DN_DetailController::class, "update"]);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}',[DN_DetailController::class, "edit"]);
    // Route for print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // Route fo prin DN label / kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);
    // Route fo get DN history
    Route::get('dn/history', [HistoryController::class, 'dnHeaderHistory']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Supplier Subcont
Route::middleware(['auth:sanctum', 'userRole:8'])->prefix('supplier-subcont')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Graph PO And DN Yearly Data
    Route::get('yearData', [DashboardController::class, 'getYearlyData']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     *  Route for Subcontractor
     */
    // Route for get list subcont item
    Route::get('item/list', [SubcontController::class,'getListItem']);
    // Route for get index subcont item (include stock)
    Route::get('item/index/{param?}', [SubcontController::class,'indexItem']);
    // Route for get index subcont transaction
    Route::get('transaction/index', [SubcontController::class,'indexTrans']);
    // Route for store subcont transaction
    Route::post('transaction/store', [SubcontController::class,'createTransaction']);

    /**
     *  Route for Delivery Note
     */
    // Route for show list DN Header
    Route::get('dn/index',[DN_HeaderController::class, "index"]);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}',[DN_DetailController::class, "edit"]);
    // Route for update list DN Detail
    Route::put('dn/update',[DN_DetailController::class, "update"]);
    // route view DN history
    Route::get('dn/history', [HistoryController::class, 'dnHeaderHistory']);
    // route view print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // route view print DN label/ kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Super User
Route::middleware(['auth:sanctum','userRole:9'])->prefix('super-user')->group(function () {
    // Route sync data
    Route::get('sync', [SynchronizeManualController::class, 'syncManual']);

    // Route get partner list
    Route::get('partner/list', [PartnerController::class, 'index']);

    /**
     *  Route for Purchase Order
     *  @param $sp_code / supplier_code is bp_code
     */
    // Routefor get record po with specific user
    Route::get('po/index/{bp_code}',[PO_HeaderController::class, "index"]);
    // Route for show PO Detail list
    Route::get('po/detail/{po_no}',[PO_DetailController::class, "index"]);
    // Route for update list PO Header
    Route::put('po/update/{po_no}',[PO_HeaderController::class, "update"]);
    // Route for print PO
    Route::get('po/print/{po_no}', [PrintController::class, 'poHeaderView']);
    // Route for show PO history list
    Route::get('po/history/{bp_code}', [HistoryController::class, 'poHeaderHistory']);

    /**
     *  Route for Delivery Note
     */
    // Route for show list DN Header
    Route::get('dn/index/{sp_code}',[DN_HeaderController::class, "indexWarehouse"]);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}',[DN_DetailController::class, "edit"]);
    // Route for update list DN Detail
    Route::put('dn/update',[DN_DetailController::class, "update"]);
    // route view DN history
    Route::get('dn/history/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);
    // route view print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    // route view print DN label/ kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelView']);

    /**
     * Route for Performance Report
     */
    // Route for show list of performance report
    Route::get('performance-report/index/{bp_code}',[ListingReportController::class, "index"])->name('index');
    // Route for download performance report
    Route::get('performance-report/file/{filename}', [ListingReportController::class, 'getFile']);
    // Route for store Listing Report
    Route::post('performance-report/store',[ListingReportController::class, "store"]);

    /**
     * Route for Forecast
     */
    // Route for get record forecast with spesific user
    Route::get('forecast/index/{bp_code}', [ForecastController::class,"indexPurchasing"]);
    // Route for store forecast file
    Route::post('forecast/store', [ForecastController::class,"store"]);
    // Route for download forecast file
    Route::get('forecast/file/{filename}', [ForecastController::class,"getFile"]);
    // Route for delete forecast file
    Route::delete('forecast/delete/{forecast}', [ForecastController::class,"destroy"]);

    /**
     *  Route for Subcontractor
     */
    // Route for get list subcont item
    Route::get('item/list', [SubcontController::class,'getListItem']);
    // Route for get index subcont item (include stock)
    Route::get('item/index/{param?}', [SubcontController::class,'indexItem']);
    // Route for get index subcont transaction
    Route::get('transaction/index', [SubcontController::class,'indexTrans']);
    // Route for store subcont transaction
    Route::post('transaction/store', [SubcontController::class,'createTransaction']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// route testing
Route::get('/listingreporttest/file/{filename}', [ListingReportController::class, 'getFile']);
Route::get('/forecasttest/file/{filename}', [ForecastController::class,"getFile"]);
