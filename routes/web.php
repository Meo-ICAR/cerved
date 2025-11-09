<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CervedEntityController;

Route::get('/', function () {
    return view('welcome');
});

// Cerved Entity Search Routes
Route::prefix('cerved/entities')->name('cerved.entity.')->group(function () {
    Route::get('/search', [CervedEntityController::class, 'showSearchForm'])->name('search.form');
    Route::get('/results', [CervedEntityController::class, 'search'])->name('search');
});
