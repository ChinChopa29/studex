<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LessonMaterialController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin,teacher,student'])->group(function () {
    Route::prefix('course')->group(function () {
        Route::get('{course}/schedule', [ScheduleController::class, 'index'])->name('CourseSchedule');
        
        Route::get('{course}/schedule/lessons/{lesson}', [ScheduleController::class, 'show'])->name('CourseScheduleShowLesson');

        Route::get('{course}/schedule/lessons/{lesson}/edit', [ScheduleController::class, 'edit'])->name('CourseScheduleEditLesson');

        Route::delete('{course}/schedule/lessons/{lesson}/edit', [ScheduleController::class, 'destroy'])->name('CourseScheduleDeleteLesson');
        
        Route::middleware(['auth:admin,teacher'])->group(function () {
            Route::get('{course}/schedule/create-lesson', [ScheduleController::class, 'create'])->name('CourseScheduleCreateLesson');
            Route::post('{course}/schedule/store-lesson', [ScheduleController::class, 'store'])->name('CourseScheduleStoreLesson');

            Route::get('{course}/schedule/lessons/{lesson}/attendance', [AttendanceController::class, 'show'])->name('CourseScheduleAttendance');
                
            Route::post('{course}/schedule/lessons/{lesson}/attendance', [AttendanceController::class, 'update'])->name('CourseScheduleUpdateAttendance');

            Route::post('{course}/schedule/lessons/{lesson}/materials', [LessonMaterialController::class, 'store'])
            ->name('CourseScheduleStoreMaterial');
         
            Route::delete('{course}/schedule/lessons/{lesson}/materials/{material}', [LessonMaterialController::class, 'destroy'])
            ->name('CourseScheduleDeleteMaterial');
        });
    });
});