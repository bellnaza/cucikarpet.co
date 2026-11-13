<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'home'])->name('home');

Route::post('/tempah', [BookingController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('booking.store');
