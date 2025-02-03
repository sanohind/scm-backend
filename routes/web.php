<?php

use Illuminate\Support\Facades\Route;

// Route Default
Route::get('', function () {
    return view('index');
});
