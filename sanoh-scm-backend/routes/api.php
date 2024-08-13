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
Route::get('edit/{user}',[UserController::class, "edit"]);
// Route for edit user data
Route::put('update/{user}',[UserController::class, "update"]);
Route::post('/create',[UserController::class, "store"]);

// Route::apiResource('/user',[UserController::class]);

// Route for show list PO Header
Route::get('/indexpoheader',[POHeaderController::class, "index"]);
// Route for edit list PO Header
Route::get('edit/{po_no}',[POHeaderController::class, "edit"]);
// Route for update list PO Header
Route::put('update/{po_no}',[POHeaderController::class, "update"]);

// Route for show list PO Detail
Route::get('/indexpodetail',[PODetailController::class, "index"]);

// Route for show list DN Header
Route::get('/indexdnheader',[DNHeaderController::class, "index"]);

// Route for show list DN Detail
Route::get('/indexdndetail',[DNDetailController::class, "index"]);
// Route for edit list DN Detail
Route::get('edit/{dn_detail_no}',[DNDetailController::class, "edit"]);
// Route for update list DN Detail
Route::put('update/{dn_detail_no}',[DNDetailController::class, "update"]);

// Route for show list of Listing Report
Route::get('/indexlistingreport',[ListingReportController::class, "index"])->name('index');
// Route for store Listing Report
Route::post('/createlistingreport',[ListingReportController::class, "store"]);
