<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RescueFoodController;

Route::get('/', [RescueFoodController::class, 'index'])->name('home');
Route::get('/api/items', [RescueFoodController::class, 'getItems']);
Route::post('/api/checkout', [RescueFoodController::class, 'checkout']);
Route::post('/api/verify-otp', [RescueFoodController::class, 'verifyOtp']);
