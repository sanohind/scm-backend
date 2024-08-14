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

