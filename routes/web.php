<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

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
Route::match (['get', 'post'], '/logout', [AuthController::class , 'logout'])->name('logout');
Route::post('/signup', [AuthController::class , 'signup'])->name('signup.submit');
Route::post('/signup-ajax', [AuthController::class , 'signupAjax'])->middleware('throttle:10,1')->name('signup.ajax');

// Doctor Apply (Guest or Auth)
Route::get('/doctor/apply', [\App\Http\Controllers\DoctorApplicationController::class , 'create'])->name('doctor.apply');
Route::post('/doctor/apply', [\App\Http\Controllers\DoctorApplicationController::class , 'store'])->name('doctor.apply.store');
Route::post('/doctor/apply/reapply', [\App\Http\Controllers\DoctorApplicationController::class , 'reapply'])->name('doctor.apply.reapply');

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
    // Network API
    Route::get('/api/profile/{user}/network', [ProfileController::class , 'network'])
        ->name('profile.network');

    // Notifications API
    Route::get('/api/notifications', [NotificationController::class , 'index'])
        ->name('notifications.index');
    Route::post('/api/notifications/read-all', [NotificationController::class , 'markAllRead'])
        ->name('notifications.readAll');
    Route::post('/api/notifications/{notification}/read', [NotificationController::class , 'markRead'])
        ->name('notifications.read');

    // Support Groups
    Route::get('/groups', [\App\Http\Controllers\GroupController::class , 'index'])->name('groups.index');
    Route::post('/groups', [\App\Http\Controllers\GroupController::class , 'store'])->name('groups.store');
    Route::get('/groups/{id}', [\App\Http\Controllers\GroupController::class , 'show'])->name('groups.show');
    Route::post('/groups/{id}/join', [\App\Http\Controllers\GroupController::class , 'join'])->name('groups.join');
    Route::post('/groups/{id}/leave', [\App\Http\Controllers\GroupController::class , 'leave'])->name('groups.leave');
    Route::put('/groups/{id}', [\App\Http\Controllers\GroupController::class , 'update'])->name('groups.update');
    Route::delete('/groups/{id}', [\App\Http\Controllers\GroupController::class , 'destroy'])->name('groups.destroy');

    // Group Cover Photo
    Route::post('/groups/{id}/update-cover', [\App\Http\Controllers\GroupController::class , 'updateCoverPhoto'])->name('groups.update.cover');
    Route::post('/groups/{id}/delete-cover', [\App\Http\Controllers\GroupController::class , 'deleteCoverPhoto'])->name('groups.delete.cover');

    // Resources
    Route::resource('resources', \App\Http\Controllers\ResourceController::class);
    Route::post('resources/{resource}/share', [\App\Http\Controllers\ResourceController::class, 'share'])->name('resources.share');
    Route::post('resources/{resource}/join', [\App\Http\Controllers\ResourceController::class, 'join'])->name('resources.join');
    Route::delete('resources/{resource}/join', [\App\Http\Controllers\ResourceController::class, 'unjoin'])->name('resources.unjoin');
    Route::get('/resource-file/{path}', [\App\Http\Controllers\ResourceController::class, 'serveFile'])
        ->where('path', '.+')
        ->name('resource.file');

    // Messenger API
    Route::get('/api/messenger/conversations', [\App\Http\Controllers\ChatController::class, 'getConversations']);
    Route::delete('/api/messenger/conversations/{conversation}', [\App\Http\Controllers\ChatController::class, 'deleteConversation']);
    Route::get('/api/messenger/messages/{conversation}', [\App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/api/messenger/send', [\App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::get('/api/messenger/search', [\App\Http\Controllers\ChatController::class, 'searchUsers']);
    Route::post('/api/messenger/typing', [\App\Http\Controllers\ChatController::class, 'setTyping']);
    Route::get('/api/messenger/typing/{conversation}', [\App\Http\Controllers\ChatController::class, 'getTyping']);
});

// ── Profile ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/posts/{post}', [ProfileController::class , 'showPost'])->name('posts.show');

    // View profile (own or others)
    Route::get('/profile/{id}', [ProfileController::class , 'show'])->name('profile.show');
    Route::post('/profile/{user}/follow', [ProfileController::class , 'toggleFollow'])->name('profile.follow');

    // Edit own profile info & photo
    Route::post('/profile/update-info', [ProfileController::class , 'updateInfo'])->name('profile.update.info');
    Route::post('/profile/update-photo', [ProfileController::class , 'updatePhoto'])->name('profile.update.photo');
    Route::post('/profile/delete-photo', [ProfileController::class , 'deletePhoto'])->name('profile.delete.photo');

    // Edit own cover photo
    Route::post('/profile/update-cover', [ProfileController::class , 'updateCoverPhoto'])->name('profile.update.cover');
    Route::post('/profile/delete-cover', [ProfileController::class , 'deleteCoverPhoto'])->name('profile.delete.cover');

    // Posts
    Route::post('/profile/posts', [ProfileController::class , 'storePost'])->name('profile.posts.store');
    Route::put('/profile/posts/{post}', [ProfileController::class , 'updatePost'])->name('profile.posts.update');
    Route::delete('/profile/posts/{post}', [ProfileController::class , 'destroyPost'])->name('profile.posts.destroy');

    // Reactions
    Route::post('/profile/posts/{post}/like', [ProfileController::class , 'toggleLike'])->name('profile.posts.like');
    Route::post('/profile/posts/{post}/share', [ProfileController::class , 'sharePost'])->name('profile.posts.share');
    Route::post('/profile/posts/{post}/save', [ProfileController::class , 'toggleSave'])->name('profile.posts.save');

    // Comments
    Route::post('/profile/posts/{post}/comments', [ProfileController::class , 'storeComment'])->name('profile.comments.store');
    Route::delete('/profile/comments/{comment}', [ProfileController::class , 'destroyComment'])->name('profile.comments.destroy');
});

// Admin Routes
Route::group(['prefix' => 'admin'], function () {

    // Admin Auth
    Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [\App\Http\Controllers\AdminAuthController::class , 'showLogin'])->name('admin.login');
            Route::post('/login', [\App\Http\Controllers\AdminAuthController::class , 'login'])->middleware('login.throttle')->name('admin.login.submit');

            Route::get('/signup', [\App\Http\Controllers\AdminAuthController::class , 'showSignup'])->name('admin.signup');
            Route::post('/signup', [\App\Http\Controllers\AdminAuthController::class , 'signup'])->name('admin.signup.submit');
        }
        );

        Route::match (['get', 'post'], '/logout', [\App\Http\Controllers\AdminAuthController::class , 'logout'])->name('admin.logout');

        // Protected Admin Routes
        Route::middleware(['auth:admin', 'admin.security'])->group(function () {
            // Dashboard/Applications
            Route::get('/applications', [\App\Http\Controllers\AdminApplicationController::class , 'index'])->name('admin.applications.index');
            Route::get('/applications/{id}', [\App\Http\Controllers\AdminApplicationController::class , 'show'])->name('admin.applications.show');
            Route::post('/applications/{id}/approve', [\App\Http\Controllers\AdminApplicationController::class , 'approve'])->name('admin.applications.approve');
            Route::post('/applications/{id}/reject', [\App\Http\Controllers\AdminApplicationController::class , 'reject'])->name('admin.applications.reject');

            // Profile
            Route::get('/profile', [\App\Http\Controllers\AdminProfileController::class , 'show'])->name('admin.profile');
            Route::post('/profile', [\App\Http\Controllers\AdminProfileController::class , 'update'])->name('admin.profile.update');
            Route::post('/profile/update-photo', [\App\Http\Controllers\AdminProfileController::class , 'updatePhoto'])->name('admin.profile.update.photo');
            Route::post('/profile/delete-photo', [\App\Http\Controllers\AdminProfileController::class , 'deletePhoto'])->name('admin.profile.delete.photo');
        }
        );
    });