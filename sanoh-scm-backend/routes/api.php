<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

// Route Login
Route::post('/login', [AuthController::class, 'login']);

//Route Supplier
Route::middleware(['auth:sanctum','userRole:supplier'])->group(function () {

    Route::get('/index', [UserController::class, 'index']);

    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});


// route testing
// Route for show list of user
// Route::get('/index',[UserController::class, "index"])->name('index');
// Route for edit user form
Route::get('edit/{user}',[UserController::class, "edit"]);
// Route for edit user data
Route::put('update/{user}',[UserController::class, "update"]);
Route::post('/create',[UserController::class, "store"]);
