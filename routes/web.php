<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PostApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleRedirectionController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');


// Authentication routes
require __DIR__.'/auth.php';

// Role-based redirection
Route::get('/redirect', [RoleRedirectionController::class, 'redirect'])
    ->middleware(['auth', 'verified'])
    ->name('redirect');

// Admin-only routes
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/posts/{id}/approve', [PostApprovalController::class, 'approve'])->name('admin.posts.approve');
    Route::post('/admin/posts/{id}/reject', [PostApprovalController::class, 'reject'])->name('admin.posts.reject');
    // Add more admin routes here
});

// User-only routes
Route::middleware(['auth', 'verified', 'user'])->group(function () {
    // Replace the closure with controller reference
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    // Add more user routes here
});

// Shared routes (accessible by both roles)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', fn () => Inertia::render('Profile'))->name('profile');
    Route::get('/settings', fn () => Inertia::render('Settings'))->name('settings');
    Route::post('/notifications/{id}/read', [PostApprovalController::class, 'markAsRead'])->name('notifications.read');
});

// Settings routes
require __DIR__.'/settings.php';
