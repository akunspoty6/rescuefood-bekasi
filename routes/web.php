<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RescueFoodController;
use App\Http\Controllers\AuthController;

// Landing / Main Application
Route::get('/', [RescueFoodController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// REST API Endpoints (Escrow & Concurrency)
Route::get('/api/items', [RescueFoodController::class, 'getItems']);
Route::post('/api/checkout', [RescueFoodController::class, 'checkout']);
Route::post('/api/verify-otp', [RescueFoodController::class, 'verifyOtp']);
