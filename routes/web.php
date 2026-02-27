<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Pages
Route::view('/about', 'about')->name('about');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
    Route::get('/signup', [AuthController::class , 'showSignup'])->name('signup');
});
Route::post('/login', [AuthController::class , 'login'])->middleware(['login.throttle'])->name('login.submit');
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');
Route::post('/signup', [AuthController::class , 'signup'])->name('signup.submit');
Route::post('/signup-ajax', [AuthController::class , 'signupAjax'])->middleware('throttle:10,1')->name('signup.ajax');

// Doctor Apply (Guest or Auth)
Route::get('/doctor/apply', [\App\Http\Controllers\DoctorApplicationController::class , 'create'])->name('doctor.apply');
Route::post('/doctor/apply', [\App\Http\Controllers\DoctorApplicationController::class , 'store'])->name('doctor.apply.store');

// Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/userdashboard', [\App\Http\Controllers\DashboardController::class , 'index'])->name('user.dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class , 'index']);

    // Dashboard feed API – throttled to 60 requests/minute
    Route::get('/api/dashboard/feed', [ProfileController::class , 'dashboardFeed'])
        ->middleware('throttle:60,1')
        ->name('dashboard.feed');

    // Search API
    Route::get('/api/search/users', [ProfileController::class , 'searchUsers'])
        ->name('dashboard.search.users');
});

// ── Profile ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // View profile (own or others)
    Route::get('/profile/{id}', [ProfileController::class , 'show'])->name('profile.show');

    // Edit own profile info & photo
    Route::post('/profile/update-info', [ProfileController::class , 'updateInfo'])->name('profile.update.info');
    Route::post('/profile/update-photo', [ProfileController::class , 'updatePhoto'])->name('profile.update.photo');
    Route::post('/profile/delete-photo', [ProfileController::class , 'deletePhoto'])->name('profile.delete.photo');

    // Posts
    Route::post('/profile/posts', [ProfileController::class , 'storePost'])->name('profile.posts.store');
    Route::put('/profile/posts/{post}', [ProfileController::class , 'updatePost'])->name('profile.posts.update');
    Route::delete('/profile/posts/{post}', [ProfileController::class , 'destroyPost'])->name('profile.posts.destroy');

    // Reactions
    Route::post('/profile/posts/{post}/like', [ProfileController::class , 'toggleLike'])->name('profile.posts.like');

    // Comments
    Route::post('/profile/posts/{post}/comments', [ProfileController::class , 'storeComment'])->name('profile.comments.store');
    Route::delete('/profile/comments/{comment}', [ProfileController::class , 'destroyComment'])->name('profile.comments.destroy');
});

// Admin Dummy Routes
Route::group(['prefix' => 'admin'], function () {
    Route::get('/applications', [\App\Http\Controllers\AdminApplicationController::class , 'index'])->name('admin.applications.index');
    Route::get('/applications/{id}', [\App\Http\Controllers\AdminApplicationController::class , 'show'])->name('admin.applications.show');
    Route::post('/applications/{id}/approve', [\App\Http\Controllers\AdminApplicationController::class , 'approve'])->name('admin.applications.approve');
    Route::post('/applications/{id}/reject', [\App\Http\Controllers\AdminApplicationController::class , 'reject'])->name('admin.applications.reject');
});