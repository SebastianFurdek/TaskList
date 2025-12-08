<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Dashboard
Route::get('/', function () { return view('welcome'); });
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Authentication (using controllers in App\Http\Controllers\Auth)
Route::get('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

// Password Reset
Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->middleware('guest')->name('password.update');

// Password Confirm
Route::get('/confirm-password', [\App\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])->middleware('auth')->name('password.confirm');
Route::post('/confirm-password', [\App\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm'])->middleware('auth');

// Email Verification
Route::get('/verify-email', \App\Http\Controllers\Auth\EmailVerificationPromptController::class)->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', \App\Http\Controllers\Auth\VerifyEmailController::class)
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// resend verification route expected by views/tests
Route::post('/email/resend', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

// Profile (custom)
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth');
Route::patch('/profile', [ProfileController::class, 'update'])->middleware('auth');
Route::delete('/profile', [ProfileController::class, 'destroy'])->middleware('auth');

// Password update for authenticated user
Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->middleware('auth');

// Tasks resource
Route::post('tasks/{task}/complete', [\App\Http\Controllers\TaskController::class, 'complete'])->name('tasks.complete')->middleware('auth');
Route::resource('tasks', TaskController::class)
    ->middleware(['auth']);

// Home fallback
Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
