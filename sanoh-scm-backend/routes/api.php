<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DN_DetailController;
use App\Http\Controllers\Api\DN_HeaderController;
use App\Http\Controllers\Api\PO_DetailController;
use App\Http\Controllers\Api\PO_HeaderController;
use App\Http\Controllers\Api\Listing_ReportController;

// Route Login
Route::post('/login', [AuthController::class, 'login']);

//Route Supplier
Route::get('/index', [UserController::class, 'index']);
Route::middleware(['auth:sanctum','userRole:supplier'])->group(function () {


    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});


// route testing
// Route for show list of user
// Route::get('/index',[UserController::class, "index"])->name('index');
// Route for edit user form
Route::post('/edit/{user}',[UserController::class, "edit"]);
// Route for edit user data
Route::put('/update/{user}',[UserController::class, "update"]);
Route::post('/create',[UserController::class, "store"]);

// Route for show list PO Header
Route::get('/indexpoheader',[PO_HeaderController::class, "index"]);
// Route for update list PO Header
Route::put('/updatepoheader/{po_no}',[PO_HeaderController::class, "update"]);

// Route for show list PO Detail
Route::get('/indexpodetail',[PO_DetailController::class, "index"]);

// Route for show list DN Header
Route::get('/indexdnheader',[DN_HeaderController::class, "index"]);

// Route for show list DN Detail
Route::get('/indexdndetail',[DN_DetailController::class, "index"]);
// Route for edit list DN Detail
Route::get('/edit/{dn_detail_no}',[DN_DetailController::class, "edit"]);
// Route for update list DN Detail
Route::put('/updatedndetail/{dn_detail_no}',[DN_DetailController::class, "update"]);

// Route for show list of Listing Report
Route::get('/indexlistingreport',[Listing_ReportController::class, "index"])->name('index');
// Route for store Listing Report
Route::post('/createlistingreport',[Listing_ReportController::class, "store"]);

/* Route::middleware('api')->group(function () {
    Route::get('/poheader', [PO_HeaderController::class, 'index']);
    Route::post('/poheader', [PO_HeaderController::class, 'store']);
    Route::get('/poheader/{po_no}', [PO_HeaderController::class, 'show']);
    Route::put('/poheader/{po_no}', [PO_HeaderController::class, 'update']);
    Route::patch('/poheader/edit/{po_no}', [PO_HeaderController::class, 'edit'])->name('po_header.edit');
}); */
