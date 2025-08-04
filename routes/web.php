<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackingController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [TrackingController::class, 'index'])
    ->name('tracking.index');

Route::post('/tracking/search', [TrackingController::class, 'search'])->name('tracking.search');
