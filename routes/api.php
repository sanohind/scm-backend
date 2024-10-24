<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ForecastController;
use App\Http\Controllers\TestMailController;
use App\Http\Controllers\Api\PrintController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\SubcontController;
use App\Http\Controllers\SynchronizeController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DN_DetailController;
use App\Http\Controllers\Api\DN_HeaderController;
use App\Http\Controllers\Api\PO_DetailController;
use App\Http\Controllers\Api\PO_HeaderController;
use App\Http\Controllers\Api\ListingReportController;
use App\Http\Controllers\SynchronizeManualController;

// Route Default
// Route::get("", function () {
//     return view("index");
// });

// Route Login
Route::post('/login', [AuthController::class, 'login']);

// route view print
Route::get('/dnhview/{no_dn}', [PrintController::class, 'dnHeaderView']);
Route::get('/lbview/{no_dn}', [PrintController::class, 'labelView']);
Route::get('/pohview/{po_no}', [PrintController::class, 'poHeaderView']);

// Route sync
Route::get('/mail', [TestMailController::class,'mail']);
Route::get('/sync', [SynchronizeController::class, 'sync']);
Route::get('/copyBusinessPartner', [SynchronizeController::class, 'copyBusinessPartner']);
Route::get('/copyPoHeader', [SynchronizeController::class, 'copyPoHeader']);
Route::get('/copyPoDetail', [SynchronizeController::class, 'copyPoDetail']);
Route::get('/copyDnHeader', [SynchronizeController::class, 'copyDnHeader']);
Route::get('/copyDnDetail', [SynchronizeController::class, 'copyDnDetail']);

//Route Supplier
Route::middleware(['auth:sanctum','userRole:1']) ->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // route view history
    Route::get('/pohistory1/{bp_code}', [HistoryController::class, 'poHeaderHistory']);
    Route::get('/dnhistory1/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    // Route for show list PO Header
    // Specific Partner
    Route::get('/indexpoheader1/{sp_code}',[PO_HeaderController::class, "index"]);
    Route::get('/indexpoheader1',[PO_HeaderController::class, "indexAll"]);
    // Route for show list PO Detail
    Route::get('/indexpodetail1/{po_no}',[PO_DetailController::class, "index"]);
    // Route for update list PO Header
    Route::put('/updatepoheader1/{po_no}',[PO_HeaderController::class, "update"]);


    // Route for show list DN Header
    Route::get('/indexdnheader1',[DN_HeaderController::class, "index"]);

    // Route for show list DN Detail
    Route::get('/indexdndetail1/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for edit list DN Detail
    Route::get('/edit1/{dn_detail_no}',[DN_DetailController::class, "edit"]);
    // Route for update list DN Detail
    Route::put('/updatedndetail1',[DN_DetailController::class, "update"]);

    // route view print
    Route::get('/pohview1/{po_no}', [PrintController::class, 'poHeaderView']);
    Route::get('/dnhview1/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('/lbview1/{no_dn}', [PrintController::class, 'labelView']);

    // Route for show list of Listing Report
    Route::get('/indexlistingreport1/{bp_code}',[ListingReportController::class, "index"]);
    Route::get('/indexlistingreport1',[ListingReportController::class, "indexAll"]);
    Route::get('/listingreport1/file/{filename}', [ListingReportController::class, 'getFile']);
    // Route for store Listing Report

    // Route for forcast
    Route::get('supplier/forecast/index',[ForecastController::class,'indexSupplier']);


    //Logout route
    Route::post('supplier/logout', [AuthController::class, 'logout']);
});


//Route Warehouse
Route::middleware(['auth:sanctum','userRole:2']) ->group(function () {

    // Route sync data
    Route::get('/syncWarehouse', [SynchronizeManualController::class, 'syncManual']);

    // Route get partner data
    Route::get('/partner2', [PartnerController::class, 'index']);

    // Route for show list DN Header
    // Specific Partner
    Route::get('/indexdnheader2/{sp_code}',[DN_HeaderController::class, "indexWarehouse"]);

    // All
    Route::get('/indexdnheader2',[DN_HeaderController::class, "indexAll"]);

    // Route for show list DN Detail
    Route::get('/indexdndetail2/{no_dn}',[DN_DetailController::class, "index"]);
    Route::get('/dnhview2/{no_dn}', [PrintController::class, 'dnHeaderView']);

    Route::get('/dnhistory2/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    //Logout route
    Route::post('warehouse/logout', [AuthController::class, 'logout']);
});

//Route Purchasing
Route::middleware(['auth:sanctum','userRole:3']) ->group(function () {
    // Route sync data
    Route::get('/syncPurchasing', [SynchronizeManualController::class, 'syncManual']);

    // Route get partner
    Route::get('/partner3', [PartnerController::class, 'index']);
    // Route for show list PO Header
    // Specific Partner
    Route::get('/indexpoheader3/{sp_code}',[PO_HeaderController::class, "index"]);
    //all
    Route::get('/indexpoheader3',[PO_HeaderController::class, "indexAll"]);

    // Route for show list PO Detail
    Route::get('/indexpodetail3/{po_no}',[PO_DetailController::class, "index"]);
    Route::get('/pohistory3/{bp_code}', [HistoryController::class, 'poHeaderHistory']);

    // Route for show list of Listing Report
    Route::get('/indexlistingreport3/{bp_code}',[ListingReportController::class, "index"])->name('index');
    Route::get('/listingreport/file/{filename}', [ListingReportController::class, 'getFile']);
    // Route for store Listing Report
    Route::post('/createlistingreport3',[ListingReportController::class, "store"]);

    Route::get('/pohview3/{po_no}', [PrintController::class, 'poHeaderView']);

    // Route for Forecast
    Route::get('/purchasing/forecast/{bp_code}', [ForecastController::class,"indexPurchasing"]);
    Route::post('/purchasing/forecast/store', [ForecastController::class,"store"]);
    Route::delete('/purchasing/forecast/delete/{forecast}', [ForecastController::class,"destroy"]);

    //Logout route
    Route::post('purchasing/logout', [AuthController::class, 'logout']);
});
//Route Super Admin
Route::middleware(['auth:sanctum','userRole:4']) ->group(function () {


    Route::get('/partner4', [PartnerController::class, 'index']);

    // Route for show list of user
    Route::get('/index4', [UserController::class, 'index']);

    // Route for create user
    Route::post('/create4',[UserController::class, "store"]);

    // Route for edit user form
    Route::get('/edit4/{user}',[UserController::class, "edit"]);

    // Route for update  user data
    Route::put('/update4/{user}',[UserController::class, "update"]);
    Route::put('/updatestatus/{user}',[UserController::class, "updateStatus"]);

    //Logout route
    Route::post('admin/logout', [AuthController::class, 'logout']);
});

// Route subcont
Route::middleware(['auth:sanctum','userRole:5']) ->group(function () {
    // Route subcont
    Route::get('/indexsubitem', [SubcontController::class,'indexItem']);
    Route::get('/indexsubtrans', [SubcontController::class,'indexTrans']);
    Route::post('/item', [SubcontController::class,'item']);
    Route::post('/subtrans', [SubcontController::class,'transaction']);
});

// route testing
// Route for show list of user
// Route::get('/index',[UserController::class, "index"])->name('index');
Route::get('/listingreporttest/file/{filename}', [ListingReportController::class, 'getFile']);
Route::get('/forecast/file/{filename}', [ForecastController::class,"getFile"]);
/* Route::middleware('api')->group(function () {
    Route::get('/poheader', [PO_HeaderController::class, 'index']);
    Route::post('/poheader', [PO_HeaderController::class, 'store']);
    Route::get('/poheader/{po_no}', [PO_HeaderController::class, 'show']);
    Route::put('/poheader/{po_no}', [PO_HeaderController::class, 'update']);
    Route::patch('/poheader/edit/{po_no}', [PO_HeaderController::class, 'edit'])->name('po_header.edit');
}); */
