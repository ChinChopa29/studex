<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin,teacher'])->group(function () {

   Route::prefix('courses')->group(function () {
      Route::get('/', [TeacherCourseController::class, 'index'])->name('teacherCoursesIndex');
      Route::get('/get-group-students', [TeacherCourseController::class, 'getGroupStudents'])->name('getGroupStudents');

      Route::get('/{course}', [TeacherCourseController::class, 'show'])->name('teacherCourseShow');

      Route::get('/{course}/exercise', [TeacherCourseController::class, 'tasksShow'])->name('teacherCourseTasks');

      Route::get('/{course}/grades', [TeacherCourseController::class, 'gradesShow'])->name('teacherCourseGrades');

      Route::get('/{course}/students', [TeacherCourseController::class, 'studentsShow'])->name('teacherCourseStudents');

      Route::get('/{course}/invite', [TeacherCourseController::class, 'inviteStudentsForm'])->name('teacherCourseInviteForm');
      Route::post('/{course}/invite-group', [TeacherCourseController::class, 'inviteStudents'])->name('teacherCourseInvite');
   });

   Route::get('/profile{student}', [UserController::class, 'studentProfile'])->name('studentProfile');
});
