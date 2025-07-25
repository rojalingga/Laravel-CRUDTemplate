<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRUDModalController;
use App\Http\Controllers\CRUDPageController;

Route::get('/', function () {
    return redirect()->route('crud-modal.index');
});

Route::resource('admin/crud-modal', CRUDModalController::class);
Route::resource('admin/crud-page', CRUDPageController::class);
