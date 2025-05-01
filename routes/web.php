<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiodataController;

Route::get('/', function () {
    return redirect()->route('biodata.index');
});

Route::resource('admin/biodata', BiodataController::class);
