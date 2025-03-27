
<?php

require __DIR__.'/course.php';
require __DIR__.'/admin.php';
require __DIR__.'/teacher.php';
require __DIR__.'/mail.php';
require __DIR__.'/task.php';
require __DIR__.'/auth.php';

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login');

Route::get('/search/{type}', [MailController::class, 'searchUsers'])->middleware('auth:admin,teacher,student');
