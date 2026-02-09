<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;


// Main Routes
Route::get('/', [TripController::class, 'dashboard'])->name('dashboard'); // Dashboard/Overview
Route::get('/map', [TripController::class, 'index'])->name('map'); // Interactive Map View
Route::get('/gallery', [TripController::class, 'gallery'])->name('gallery'); // Photo Gallery
Route::get('/bucket-list', [TripController::class, 'bucketList'])->name('bucket-list'); // Bucket List

// Trip CRUD Routes
Route::post('/trip', [TripController::class, 'store'])->name('trip.store');
Route::put('/trip/{id}', [TripController::class, 'update'])->name('trip.update');
Route::delete('/trip/{id}', [TripController::class, 'destroy'])->name('trip.destroy');

// Bucket List CRUD Routes
Route::post('/bucket', [TripController::class, 'storeBucket'])->name('bucket.store');
Route::delete('/bucket/{id}', [TripController::class, 'destroyBucket'])->name('bucket.destroy');
Route::put('/bucket/{id}', [TripController::class, 'updateBucket'])->name('bucket.update');


