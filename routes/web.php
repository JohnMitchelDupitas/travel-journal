<?php

use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TripController::class, 'index']);
Route::post('/trip', [TripController::class, 'store'])->name('trip.store');
Route::delete('/trip/{id}', [TripController::class, 'destroy'])->name('trip.destroy');