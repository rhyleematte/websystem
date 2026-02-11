<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ✅ Root URL is the login page
Route::get('/', [AuthController::class , 'showLogin'])->name('login');

// (Optional) also allow /login to show the same page
Route::get('/login', [AuthController::class , 'showLogin']);

// Login / logout actions
Route::post('/login', [AuthController::class , 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// ✅ Protected dashboard (only logged-in users)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');
