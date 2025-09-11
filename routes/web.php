<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\LandingPageController;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

Route::get('/', [LandingPageController::class, 'index'])->name('landing-page.index');

Route::get('/tracking', [TrackingController::class, 'index'])
    ->name('tracking.index');

Route::post('/tracking/search', [TrackingController::class, 'search'])
    ->name('tracking.search');

// New route for displaying tracking with invoice parameter
Route::get('/tracking/{invoice}', [TrackingController::class, 'show'])
    ->name('tracking.show');

Route::get('/sitemap.xml', function () {
    // Membuat sitemap hanya untuk halaman landing page
    $sitemap = Sitemap::create()
        ->add(Url::create(url('/'))); // Hanya menambahkan URL landing page

    // Mengembalikan sitemap dalam format XML
    return response($sitemap->render())
        ->header('Content-Type', 'application/xml');
});
