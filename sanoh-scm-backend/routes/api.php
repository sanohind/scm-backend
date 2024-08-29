<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PrintController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\SynchronizeController;
use App\Http\Controllers\Api\DN_DetailController;
use App\Http\Controllers\Api\DN_HeaderController;
use App\Http\Controllers\Api\PO_DetailController;
use App\Http\Controllers\Api\PO_HeaderController;
use App\Http\Controllers\Api\ListingReportController;

// Route Login
Route::post('/login', [AuthController::class, 'login']);

// Route sync
Route::get('/sync', [SynchronizeController::class, 'sync']);
Route::get('/copyBusinessPartner', [SynchronizeController::class, 'copyBusinessPartner']);
Route::get('/copyPoHeader', [SynchronizeController::class, 'copyPoHeader']);
Route::get('/copyPoDetail', [SynchronizeController::class, 'copyPoDetail']);
Route::get('/copyDnHeader', [SynchronizeController::class, 'copyDnHeader']);
Route::get('/copyDnDetail', [SynchronizeController::class, 'copyDnDetail']);

//Route Supplier
Route::middleware(['auth:sanctum','userRole:1']) ->group(function () {


    // route view history
    Route::get('/pohistory/{bp_code}', [HistoryController::class, 'poHeaderHistory']);
    Route::get('/dnhistory/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    // route view print
    Route::get('/pohview/{po_no}', [PrintController::class, 'poHeaderView']);
    Route::get('/dnhview/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('/lbview/{no_dn}', [PrintController::class, 'labelView']);

    // Route for show list PO Header
    Route::get('/indexpoheader',[PO_HeaderController::class, "index"]);
    // Route for show list PO Detail
    Route::get('/indexpodetail/{po_no}',[PO_DetailController::class, "index"]);
    // Route for update list PO Header
    Route::put('/updatepoheader/{po_no}',[PO_HeaderController::class, "update"]);


    // Route for show list DN Header
    Route::get('/indexdnheader',[DN_HeaderController::class, "index"]);

    // Route for show list DN Detail
    Route::get('/indexdndetail/{no_dn}',[DN_DetailController::class, "index"]);
    // Route for edit list DN Detail
    Route::get('/edit/{dn_detail_no}',[DN_DetailController::class, "edit"]);
    // Route for update list DN Detail
    Route::put('/updatedndetail/{dn_detail_no}',[DN_DetailController::class, "update"]);


    // Route for show list of Listing Report
    Route::get('/indexlistingreport',[ListingReportController::class, "index"])->name('index');
    // Route for store Listing Report

    // route view print
    Route::get('/pohview/{po_no}', [PrintController::class, 'poHeaderView']);
    Route::get('/dnhview/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('/lbview/{no_dn}', [PrintController::class, 'labelView']);

    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});

//Route Warehouse
Route::middleware(['auth:sanctum','userRole:2']) ->group(function () {

    Route::get('/partner', [PartnerController::class, 'index']);

    // Route for show list DN Header
    Route::get('/indexdnheader',[DN_HeaderController::class, "index"]);

    // Route for show list DN Detail
    Route::get('/indexdndetail/{no_dn}',[DN_DetailController::class, "index"]);

    Route::get('/dnhview/{no_dn}', [PrintController::class, 'dnHeaderView']);

    Route::get('/dnhistory/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});

//Route Purchasing
Route::middleware(['auth:sanctum','userRole:3']) ->group(function () {

    Route::get('/partner', [PartnerController::class, 'index']);
    // Route for show list PO Header
    Route::get('/indexpoheader',[PO_HeaderController::class, "index"]);
    // Route for show list PO Detail
    Route::get('/indexpodetail/{po_no}',[PO_DetailController::class, "index"]);
    Route::get('/pohistory/{bp_code}', [HistoryController::class, 'poHeaderHistory']);

    // Route for show list of Listing Report
    Route::get('/indexlistingreport',[ListingReportController::class, "index"])->name('index');
    // Route for store Listing Report
    Route::post('/createlistingreport',[ListingReportController::class, "store"]);

    Route::get('/pohview/{po_no}', [PrintController::class, 'poHeaderView']);
    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});

//Route Super Admin
Route::middleware(['auth:sanctum','userRole:4']) ->group(function () {

    Route::get('/partner', [PartnerController::class, 'index']);

    // Route for show list of user
    Route::get('/index', [UserController::class, 'index']);
    // Route for edit user form
    Route::post('/edit/{user}',[UserController::class, "edit"]);
    // Route for edit user data
    Route::put('/update/{user}',[UserController::class, "update"]);
    Route::post('/create',[UserController::class, "store"]);
    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});


// route testing
// Route for show list of user
// Route::get('/index',[UserController::class, "index"])->name('index');





/* Route::middleware('api')->group(function () {
    Route::get('/poheader', [PO_HeaderController::class, 'index']);
    Route::post('/poheader', [PO_HeaderController::class, 'store']);
    Route::get('/poheader/{po_no}', [PO_HeaderController::class, 'show']);
    Route::put('/poheader/{po_no}', [PO_HeaderController::class, 'update']);
    Route::patch('/poheader/edit/{po_no}', [PO_HeaderController::class, 'edit'])->name('po_header.edit');
}); */
