<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin,teacher'])->group(function () {

   Route::prefix('course')->group(function () {

      Route::get('/{group_id}/get-group-students', [TeacherCourseController::class, 'getGroupStudents'])->name('getGroupStudents');

      Route::get('/{course}/invite', [TeacherCourseController::class, 'inviteStudentsForm'])->name('teacherCourseInviteForm');
      Route::post('/{course}/invite-group', [TeacherCourseController::class, 'inviteStudents'])->name('teacherCourseInvite');
   });

});

