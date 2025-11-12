<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CervedEntityController;

Route::get('/', function () {
    return view('welcome');
});

// API Logs
Route::prefix('logs/api-cerved')->name('logs.api-cerved.')->group(function () {
    Route::get('/', [\App\Http\Controllers\LogApiCervedController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\LogApiCervedController::class, 'show'])->name('show');
});

// Route to test file upload
Route::get('/upload-test', function () {
    return view('upload');
});

// Cerved Entity Search Routes
Route::prefix('cerved/entities')->name('cerved.entity.')->group(function () {
    Route::get('/search', [CervedEntityController::class, 'showSearchForm'])->name('search.form');
    Route::get('/results', [CervedEntityController::class, 'search'])->name('search');
});
