<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\TestTaskController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin,teacher,student'])->group(function () {

   Route::prefix('course')->group(function () {

      Route::middleware(['auth:admin,teacher'])->group(function () {
         Route::get('/{course}/create-task-form', [TaskController::class, 'create'])->name('teacherCourseCreateTask');
         Route::post('/{course}/create-task', [TaskController::class, 'store'])->name('teacherCourseStoreTask'); 

         Route::post('/courses/{course}/tasks/test', [TestTaskController::class, 'store'])
         ->name('teacherCourseStoreTestTask')
         ->middleware('auth:teacher');
 
         Route::get('/{course}/create-milestone-form', [MilestoneController::class, 'create'])->name('teacherCourseCreateMilestone');
         Route::post('/{course}/create-milestone', [MilestoneController::class, 'store'])->name('teacherCourseStoreMilestone');
      });

      Route::get('/{course}/tasks', [TaskController::class, 'index'])->name('CourseTasks');
      Route::get('/{course}/{task}', [TaskController::class, 'show'])->name('CourseTask');

      Route::get('/{course}/test/{testTask}', [TaskController::class, 'showTest'])->name('CourseTestTask');

      Route::middleware(['auth:student'])->group(function () {
         Route::post('/{course}/{task}/upload', [TaskController::class, 'upload'])->name('CourseTaskUpload');

         Route::post('/courses/{course}/test-tasks/{testTask}/submit', [TaskController::class, 'submitTest'])->name('SubmitTestTask');
      });

      Route::middleware(['auth:admin,teacher'])->group(function () {
         Route::put('/{course}/update-milestone/{milestone}', [MilestoneController::class, 'update'])
         ->name('teacherCourseUpdateMilestone');

         Route::delete('/{course}/delete-milestone/{milestone}', [MilestoneController::class, 'destroy'])->name('teacherCourseDestroyMilestone');

         Route::get('/{course}/{task}/edit', [TaskController::class, 'edit'])->name('CourseTaskEdit');
         Route::put('/{course}/{task}/update', [TaskController::class, 'update'])->name('CourseTaskUpdate');

         Route::get('/{course}/test/{testTask}/edit', [TaskController::class, 'editTest'])->name('CourseTestEdit');
         Route::put('/{course}/test/{testTask}/update', [TaskController::class, 'updateTest'])->name('CourseTeskUpdate');

         Route::delete('/{course}/{task}/delete', [TaskController::class, 'destroy'])->name('CourseTaskDelete');

         Route::delete('/{course}/test/{testTask}/delete', [TaskController::class, 'destroyTest'])->name('CourseTestDelete');

         Route::get('/{course}/{task}/{student}', [TaskController::class, 'showStudentTask'])->name('CourseTaskShowStudent');
         Route::post('/{course}/{task}/{student}/grade', [TaskController::class, 'gradeStudentTask'])->name('CourseTaskGradeStudent');

         Route::get('/{course}/test/{testTask}/{student}', [TaskController::class, 'showStudentTest'])->name('CourseTestShowStudent');
         
      });
   });

});