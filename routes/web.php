<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FolderController;

Route::get('/', fn () => redirect('/login'))->middleware('guest');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // ✅ Forgot & Reset Password
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [FolderController::class, 'index'])->name('dashboard');

    Route::prefix('folders')->name('folders.')->group(function () {
        Route::get('/create', [FolderController::class, 'create'])->name('create');
        Route::post('/', [FolderController::class, 'store'])->name('store');
        Route::get('/{id}', [FolderController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [FolderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FolderController::class, 'update'])->name('update');
        Route::delete('/{id}', [FolderController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/create/{folder_id}', [DocumentController::class, 'create'])->name('create');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('{id}/edit', [DocumentController::class, 'edit'])->name('edit');
        Route::put('{id}', [DocumentController::class, 'update'])->name('update');
        Route::delete('{id}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::get('{id}/preview', [DocumentController::class, 'preview'])->name('preview');
    });

    // ✅ Semua route untuk profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

    Route::get('/profile/edit-name', [ProfileController::class, 'editName'])->name('profile.edit.name');
    Route::post('/profile/update-name', [ProfileController::class, 'updateName'])->name('profile.update.name');

    Route::get('/profile/edit-password', [ProfileController::class, 'editPassword'])->name('profile.edit.password');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');

    Route::get('/profile/edit-photo', [ProfileController::class, 'editPhoto'])->name('profile.edit.photo');
    Route::post('/profile/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
});
