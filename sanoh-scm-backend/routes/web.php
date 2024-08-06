<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerController;

Route::get('/', [PartnerController::class,'index']);
