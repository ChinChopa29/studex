
<?php

require __DIR__.'/api.php';
require __DIR__.'/course.php';
require __DIR__.'/schedule.php';
require __DIR__.'/admin.php';
require __DIR__.'/teacher.php';
require __DIR__.'/mail.php';
require __DIR__.'/task.php';
require __DIR__.'/accounting.php';
require __DIR__.'/account.php';
require __DIR__.'/auth.php';

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login');

Route::get('/groups', [GroupController::class, 'showGroups'])->name('showGroups');

Route::get('/info', function (){
   return view('info');
})->name('info');

Route::get('/search/{type}', [MailController::class, 'searchUsers'])->middleware('auth:admin,teacher,student');




