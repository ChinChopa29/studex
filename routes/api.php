<?php

use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/course/{course}/schedule/lessons', [ScheduleController::class, 'getLessons']);

Route::get('/romka/{course}', [ScheduleController::class, 'getLessons']);
