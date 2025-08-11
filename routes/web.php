<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackingController;

Route::redirect('/', '/tracking');

Route::get('/tracking', [TrackingController::class, 'index'])
    ->name('tracking.index');

Route::post('/tracking/search', [TrackingController::class, 'search'])
    ->name('tracking.search');

// New route for displaying tracking with invoice parameter
Route::get('/tracking/{invoice}', [TrackingController::class, 'show'])
    ->name('tracking.show');
