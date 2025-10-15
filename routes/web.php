<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirect root to login or workflows based on auth status
Route::get('/', function () {
    return auth()->check() ? redirect('/workflows') : redirect('/login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'user' => [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ],
        'stats' => [
            'users' => 150,
            'projects' => 25,
            'tasks' => 342,
        ],
    ]);
})->name('dashboard');

// ========================================
// Authentication Routes (Public)
// ========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');

    Route::get('/register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');

    Route::get('/forgot-password', function () {
        return Inertia::render('Auth/ForgotPassword');
    })->name('password.request');

    Route::get('/reset-password/{token}', function ($token) {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    })->name('password.reset');
});

// ========================================
// Protected Routes (Requires Authentication)
// ========================================
Route::middleware(['auth'])->group(function () {
    // Profile & Settings Routes
    Route::get('/profile/settings', function () {
        return Inertia::render('Profile/Settings');
    })->name('profile.settings');

    // Make workflow routes protected
    Route::get('/workflows', function () {
        return Inertia::render('Workflows/Index');
    })->name('workflows.index');

    Route::get('/workflows/create', function () {
        return Inertia::render('Workflows/Edit');
    })->name('workflows.create');

    Route::get('/workflows/{id}/edit', function ($id) {
        return Inertia::render('Workflows/Edit', [
            'workflowId' => $id,
        ]);
    })->name('workflows.edit');

    // Protected execution routes
    Route::get('/executions', function () {
        return Inertia::render('Executions/Index');
    })->name('executions.index');

    // Protected credential routes
    Route::get('/credentials', function () {
        return Inertia::render('Credentials/Index');
    })->name('credentials.index');

    // Protected template routes
    Route::get('/templates', function () {
        return Inertia::render('Templates/Index');
    })->name('templates.index');
});
