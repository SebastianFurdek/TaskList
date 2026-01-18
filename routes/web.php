<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Authentication - Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authentication - Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Password Reset
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// Profile - custom
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth');
Route::patch('/profile', [ProfileController::class, 'update'])->middleware('auth');
Route::delete('/profile', [ProfileController::class, 'destroy'])->middleware('auth');

// Tasks - custom
Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete')->middleware('auth');
Route::resource('tasks', TaskController::class)->middleware('auth');

//projects
use App\Http\Controllers\ProjectController;
Route::middleware(['auth'])->group(function () {
    Route::resource('/projects', ProjectController::class);
});

// Categories routes
use App\Http\Controllers\CategoryController;
Route::middleware(['auth'])->group(function () {
    Route::resource('/categories', CategoryController::class)->except(['show']);
});
