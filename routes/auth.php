<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('register/cancel-oauth', [AuthController::class, 'cancelOauthRegistration'])->name('register.cancel-oauth');
    Route::get('register/success', [AuthController::class, 'showRegisterSuccess'])->name('register.success');

    // Email verification routes
    Route::get('email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::get('email/verify/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('email/verify/resend', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');

    // Password Reset Routes
    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
