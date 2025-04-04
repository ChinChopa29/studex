
<?php

require __DIR__.'/api.php';
require __DIR__.'/course.php';
require __DIR__.'/schedule.php';
require __DIR__.'/admin.php';
require __DIR__.'/teacher.php';
require __DIR__.'/mail.php';
require __DIR__.'/task.php';
require __DIR__.'/auth.php';


use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login');

Route::get('/search/{type}', [MailController::class, 'searchUsers'])->middleware('auth:admin,teacher,student');

Route::get('/debug-schedule', function() {
   $courseId = 6;
   $start = '2025-03-31';
   $end = '2025-04-06';
   
   // 1. Проверим все занятия курса
   $allLessons = \App\Models\Schedule::where('course_id', $courseId)->get();
   dump("Все занятия курса:", $allLessons->toArray());
   
   // 2. Проверим разовые занятия в периоде
   $singleLessons = \App\Models\Schedule::where('course_id', $courseId)
       ->whereNull('recurrence')
       ->whereBetween('date', [$start, $end])
       ->get();
   dump("Разовые занятия в периоде:", $singleLessons->toArray());
   
   // 3. Проверим условия запроса
   $testQuery = \App\Models\Schedule::where('course_id', $courseId)
       ->where(function($query) use ($start, $end) {
           $query->whereNull('recurrence')
               ->whereBetween('date', [$start, $end])
               ->orWhere(function($q) use ($end, $start) {
                   $q->whereNotNull('recurrence')
                     ->where('date', '<=', $end)
                     ->where('recurrence_end_date', '>=', $start);
               });
       })
       ->toSql();
   dump("SQL запрос:", $testQuery);
   
   // 4. Выполним полный запрос
   $result = \App\Models\Schedule::where('course_id', $courseId)
       ->where(function($query) use ($start, $end) {
           $query->whereNull('recurrence')
               ->whereBetween('date', [$start, $end])
               ->orWhere(function($q) use ($end, $start) {
                   $q->whereNotNull('recurrence')
                     ->where('date', '<=', $end)
                     ->where('recurrence_end_date', '>=', $start);
               });
       })
       ->get();
   dump("Результат запроса:", $result->toArray());
   
   // 5. Проверим конкретное занятие
   $specificLesson = \App\Models\Schedule::find(1);
   dump("Занятие ID 1:", $specificLesson->toArray());
   dump("recurrence is null:", is_null($specificLesson->recurrence));
   dump("date between:", $specificLesson->date >= $start && $specificLesson->date <= $end);
});


