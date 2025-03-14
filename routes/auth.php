<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
   Route::post('/auth', [AuthController::class, 'authenticate'])->name('auth');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');