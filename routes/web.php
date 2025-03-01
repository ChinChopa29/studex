<?php

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';
use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


Route::get('/main', [MainController::class, 'index'])->name('index')->middleware('auth');

Route::get('/profile{student}', [UserController::class, 'studentProfile'])->name('studentProfile')->middleware('auth');


