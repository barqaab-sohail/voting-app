<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VotingSessionController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AdminController;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('voting-sessions', VotingSessionController::class);
    Route::get('voting-sessions/{votingSession}/vote', [VoteController::class, 'show'])
        ->name('votes.show');
    Route::post('voting-sessions/{votingSession}/vote', [VoteController::class, 'store'])
        ->name('votes.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('members', AdminController::class)->except(['show']);
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Google login routes
Route::get('/login/google', [App\Http\Controllers\Auth\GoogleLoginController::class, 'redirectToGoogle'])
    ->name('login.google');
Route::get('/login/google/callback', [App\Http\Controllers\Auth\GoogleLoginController::class, 'handleGoogleCallback']);
