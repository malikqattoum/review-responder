<?php

use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('pages.landing');
})->name('landing');

// Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Protected pages (basic check - redirect to login if no token in localStorage)
// Actual protection is handled client-side via API calls

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::get('/reviews', function () {
    return view('pages.reviews');
})->name('reviews');

Route::get('/settings', function () {
    return view('pages.settings');
})->name('settings');

Route::get('/billing', function () {
    return view('pages.billing');
})->name('billing');

// Health check
Route::get('/api/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
