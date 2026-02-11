<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Pages
Route::view('/about', 'about')->name('about');

// Auth
Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
Route::post('/login', [AuthController::class , 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');
Route::get('/signup', [AuthController::class , 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class , 'signup'])->name('signup.submit');
Route::view('/userdashboard', 'userdashboard')->name('user.dashboard');
