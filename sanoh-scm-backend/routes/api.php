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
    Route::get('/pohistory1/{bp_code}', [HistoryController::class, 'poHeaderHistory']);
    Route::get('/dnhistory1/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    // route view print
    Route::get('/pohview1/{po_no}', [PrintController::class, 'poHeaderView']);
    Route::get('/dnhview1/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('/lbview1/{no_dn}', [PrintController::class, 'labelView']);

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
    Route::put('/updatedndetail1/{dn_detail_no}',[DN_DetailController::class, "update"]);


    // Route for show list of Listing Report
    Route::get('/indexlistingreport1',[ListingReportController::class, "index"])->name('index');
    // Route for store Listing Report

    // route view print
    Route::get('/pohview1/{po_no}', [PrintController::class, 'poHeaderView']);
    Route::get('/dnhview1/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('/lbview1/{no_dn}', [PrintController::class, 'labelView']);

    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});

//Route Warehouse
Route::middleware(['auth:sanctum','userRole:2']) ->group(function () {

    Route::get('/partner2', [PartnerController::class, 'index']);

    // Route for show list DN Header
    // Specific Partner
    Route::get('/indexdnheader2/{po_no}',[DN_HeaderController::class, "index"]);

    // All
    Route::get('/indexdnheader2',[DN_HeaderController::class, "indexAll"]);

    // Route for show list DN Detail
    Route::get('/indexdndetail2/{no_dn}',[DN_DetailController::class, "index"]);

    Route::get('/dnhview2/{no_dn}', [PrintController::class, 'dnHeaderView']);

    Route::get('/dnhistory2/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});

//Route Purchasing
Route::middleware(['auth:sanctum','userRole:3']) ->group(function () {

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
    Route::get('/indexlistingreport3',[ListingReportController::class, "index"])->name('index');
    // Route for store Listing Report
    Route::post('/createlistingreport3',[ListingReportController::class, "store"]);

    Route::get('/pohview3/{po_no}', [PrintController::class, 'poHeaderView']);
    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});

//Route Super Admin
Route::middleware(['auth:sanctum','userRole:4']) ->group(function () {


    Route::get('/partner4', [PartnerController::class, 'index']);
    // Route for show list of user
    Route::get('/index4', [UserController::class, 'index']);
    // Route for edit user form
    Route::post('/edit4/{user}',[UserController::class, "edit"]);
    // Route for edit user data
    Route::put('/updatestatus/{user}',[UserController::class, "updateStatus"]);
    Route::post('/create4',[UserController::class, "store"]);
    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::put('/update4/{user}',[UserController::class, "update"]);
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
