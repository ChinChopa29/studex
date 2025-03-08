<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:admin,student'])->group(function () {

   Route::prefix('courses')->group(function () {
      Route::get('/', [StudentCourseController::class, 'index'])->name('studentCoursesIndex');

      Route::get('/{course}', [StudentCourseController::class, 'show'])->name('studentCourseShow');

      Route::get('/{course}/exercise', [StudentCourseController::class, 'tasksShow'])->name('studentCourseTasks');

      Route::get('/{course}/grades', [StudentCourseController::class, 'gradesShow'])->name('studentCourseGrades');

      Route::get('/{course}/students', [StudentCourseController::class, 'studentsShow'])->name('studentCourseStudents');
   });

   Route::get('/profile{student}', [UserController::class, 'studentProfile'])->name('studentProfile');
});