<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth', [AuthController::class, 'authenticate'])->name('auth');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



