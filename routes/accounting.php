<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:admin,teacher'])->group(function () {
   Route::get('{course}/accounting/attendance/{student}', [AccountingController::class, 'studentAttendance'])->name('CourseStudentAttendance');
   Route::get('{course}/accounting/export-attendance/{student}', [AccountingController::class, 'exportStudentAttendance'])->name('exportStudentAttendance');

   Route::get('{course}/accounting/assignment/{student}', [AccountingController::class, 'studentAssignment'])->name('CourseStudentAssignment');
   Route::get('{course}/accounting/export-assignment/{student}', [AccountingController::class, 'exportStudentAssignment'])->name('exportStudentAssignment');

   Route::get('{course}/accounting/attendance', [AccountingController::class, 'attendance'])->name('CourseAttendance');
   Route::get('{course}/accounting/export-attendance', [AccountingController::class, 'exportAttendance'])->name('exportAttendanceReport');

   Route::get('{course}/accounting/assignment', [AccountingController::class, 'assignment'])->name('CourseAssignment');
   Route::get('{course}/accounting/export-assignment', [AccountingController::class, 'exportAssignment'])->name('exportAssignmentReport');

   Route::get('{course}/accounting/performance', [AccountingController::class, 'performance'])->name('CoursePerformance');
   Route::get('{course}/accounting/export-performance', [AccountingController::class, 'exportPerformance'])->name('exportPerformanceReport');
});




