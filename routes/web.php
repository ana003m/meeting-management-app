<?php

use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MeetingNoteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes - Login
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Auth Routes - Register
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::resource('meetings', MeetingController::class);
    Route::post('/meetings/{meeting}/notes', [MeetingNoteController::class, 'store'])->name('meetings.notes.store');
    Route::post('/meetings/{meeting}/notes/{note}/generate', [MeetingNoteController::class, 'generate'])->name('meetings.notes.generate');
    Route::get('/meetings/{meeting}/minutes', [MeetingController::class, 'minutesIndex'])->name('meetings.minutes.index');
    Route::get('/meetings/{meeting}/minutes/{minutes}', [MeetingController::class, 'showMinutes'])->name('meetings.minutes.show');
});
