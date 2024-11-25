<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// Landing page with 'Login' and 'Register' buttons
Route::view('/', 'welcome');

// Login and register pages
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register');

// Protected home route for to-do list (only accessible to logged-in users)
Route::middleware('auth:sanctum')->group(function () {
    Route::view('/home', 'home');
});

// Redirect user to login page if not authenticated
Route::get('/home', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
});