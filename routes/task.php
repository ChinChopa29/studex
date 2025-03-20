<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin,teacher,student'])->group(function () {

   Route::prefix('course')->group(function () {

      Route::get('/{course}/create-task-form', [TaskController::class, 'create'])->name('teacherCourseCreateTask');
      Route::post('/{course}/create-task', [TaskController::class, 'store'])->name('teacherCourseStoreTask');

      Route::get('/{course}/tasks', [TaskController::class, 'index'])->name('CourseTasks');
      Route::get('/{course}/{task}', [TaskController::class, 'show'])->name('CourseTask');
   });

});

