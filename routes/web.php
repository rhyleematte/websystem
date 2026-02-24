<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

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
Route::post('/signup', [AuthController::class , 'signup'])->name('signup.submit'); // optional normal submit
Route::post('/signup-ajax', [AuthController::class , 'signupAjax'])->name('signup.ajax'); // ✅ AJAX

// Dashboard
Route::view('/userdashboard', 'userdashboard')->middleware('auth')->name('user.dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
});