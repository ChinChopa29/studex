<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:admin,teacher,student'])->group(function () {

   Route::prefix('course')->group(function () {

      Route::get('/', [TeacherCourseController::class, 'index'])->name('CoursesIndex');

      Route::post('/course/changeColor', [StudentCourseController::class, 'storeColor'])->name('ChangeCourseColor');

      Route::get('/{course}/students', [TeacherCourseController::class, 'studentsShow'])->name('CourseStudents');

      Route::get('/{course}/grades', [TeacherCourseController::class, 'gradesShow'])->name('CourseGrades');

      Route::get('/{course}', [TeacherCourseController::class, 'show'])->name('CourseShow');

   });

});




