<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::get('/profile/{type}/{id}', [AccountController::class, 'index'])->name('UserProfile')->middleware('auth:admin,teacher,student');

Route::put('/profile/{type}/{id}/update-password', [AccountController::class, 'updatePassword'])->name('UpdatePassword')->middleware('auth:admin,teacher,student');

