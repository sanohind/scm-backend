<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DNDetailController;
use App\Http\Controllers\DNHeaderController;
use App\Http\Controllers\PODetailController;
use App\Http\Controllers\POHeaderController;
use App\Http\Controllers\ListingReportController;

// Route for super admin
// Route for show list of user
Route::get('/index',[UserController::class, "index"])->name('index');
// Route for edit user form
Route::post('edit/{user}',[UserController::class, "edit"]);
// Route for edit user data
Route::put('/update/{user}',[UserController::class, "update"]);
Route::post('/create',[UserController::class, "store"]);

// Route::apiResource('/user',[UserController::class]);

// Route for show list PO Header
Route::get('/indexpoheader',[PO_HeaderController::class, "index"]);
// Route for edit list PO Header
Route::get('edit/{po_no}',[PO_HeaderController::class, "edit"]);
// Route for update list PO Header
Route::put('/updatepoheader/{po_no}',[PO_HeaderController::class, "update"]);

// Route for show list PO Detail
Route::get('/indexpodetail',[PO_DetailController::class, "index"]);

// Route for show list DN Header
Route::get('/indexdnheader',[DN_HeaderController::class, "index"]);

// Route for show list DN Detail
Route::get('/indexdndetail',[DN_DetailController::class, "index"]);
// Route for edit list DN Detail
Route::get('edit/{dn_detail_no}',[DN_DetailController::class, "edit"]);
// Route for update list DN Detail
Route::put('/updatedndetail/{dn_detail_no}',[DN_DetailController::class, "update"]);

// Route for show list of Listing Report
Route::get('/indexlistingreport',[Listing_ReportController::class, "index"])->name('index');
// Route for store Listing Report
Route::post('/createlistingreport',[Listing_ReportController::class, "store"]);
