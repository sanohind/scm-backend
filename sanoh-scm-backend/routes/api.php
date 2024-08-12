<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route for super admin
// Route for show list of user
Route::get('/index',[UserController::class, "index"])->name('index');
// Route for edit user form
Route::get('edit/{user}',[UserController::class, "edit"]);
// Route for edit user data
Route::put('update/{user}',[UserController::class, "update"]);
Route::post('/create',[UserController::class, "store"]);

// Route::apiResource('/user',[UserController::class]);
