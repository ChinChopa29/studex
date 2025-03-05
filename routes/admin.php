<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\EducationProgramController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['is_admin'])->prefix('admin')->group(function () {
   
   // Панель управления системой
   Route::get('/', [AdminController::class, 'index'])->name('admin.index');

   // Поиск групп
   Route::get('group/search', [GroupController::class, 'search'])->name('admin.searchGroup');

   // Акроним для групп
   Route::get('/group/search-acronym', [GroupController::class, 'searchAcronym']);
   Route::get('/group/search-acronym-subgroup', [GroupController::class, 'searchAcronymSubGroup']);

   // Группы
   Route::prefix('groups')->group(function () {
      // Создание группы
      Route::get('/create', [GroupController::class, 'create'])->name('admin.createGroup');
      Route::post('/store', [GroupController::class, 'store'])->name('admin.storeGroup');

      // Создание подгрупп
      Route::get('/create/subgroup', [GroupController::class, 'createSubgroup'])->name('admin.createSubgroup');
      Route::post('/store/subgroup', [GroupController::class, 'storeSubgroup'])->name('admin.storeSubgroup');

      // Добавление студентов в группу/подгруппу
      Route::get('/{group}/addStudent', [GroupController::class, 'addStudent'])->name('admin.addStudent');
      Route::post('/{group}/attachStudent/{student}', [GroupController::class, 'attachStudent'])->name('admin.attachStudent');
      
      // Отображение групп
      Route::get('/', [GroupController::class, 'index'])->name('admin.showGroups');
      Route::get('/{group}', [GroupController::class, 'show'])->name('admin.showGroup');

      // Редактирование группы
      Route::get('/{group}/edit', [GroupController::class, 'edit'])->name('admin.editGroup');
      Route::put('/{group}/update', [GroupController::class, 'update'])->name('admin.updateGroup');

      // Удаление студента из группы
      Route::delete('/{group}/detach-student/{student}', [GroupController::class, 'detachUser'])->name('admin.detachUser');

      // Удаление преподавателя из группы
      Route::delete('/{group}/detach-teacher/{teacher}', [GroupController::class, 'detachTeacher'])->name('admin.detachTeacher');

      // Удаление группы
      Route::delete('/{group}/delete', [GroupController::class, 'destroy'])->name('admin.destroyGroup');
   });

   // Поиск курсов
   Route::get('course/search', [CoursController::class, 'search'])->name('admin.searchCourse');

   // Акроним для курсов
   Route::get('/courses/search-code', [CoursController::class, 'searchCode']);

   // Курсы
   Route::prefix('courses')->group(function () {
      // Создание курса
      Route::get('/create', [CoursController::class, 'create'])->name('admin.createCourse');
      Route::post('/store', [CoursController::class, 'store'])->name('admin.storeCourse');
      
      // Отображение курсов
      Route::get('/', [CoursController::class, 'index'])->name('admin.showCourses');
      Route::get('/{course}', [CoursController::class, 'show'])->name('admin.showCourse');

      // Редактирование курса
      Route::get('/{course}/edit', [CoursController::class, 'edit'])->name('admin.editCourse');
      Route::put('/{course}/update', [CoursController::class, 'update'])->name('admin.updateCourse');

      // // Удаление студента из группы
      // Route::delete('/{group}/{student}/delete', [GroupController::class, 'detach'])->name('admin.detachUser');

      // Удаление курса
      Route::delete('/{course}/delete', [CoursController::class, 'destroy'])->name('admin.destroyCourse');
   });

   // Поиск программ
   Route::get('education-program/search', [EducationProgramController::class, 'search'])->name('admin.searchProgram');
   
   // Образовательные программы
   Route::prefix('education-program')->group(function () {
      // Создание программы
      Route::get('/create', [EducationProgramController::class, 'create'])->name('admin.createProgram');
      Route::post('/store', [EducationProgramController::class, 'store'])->name('admin.storeProgram');
      
      // Отображение программ
      Route::get('/', [EducationProgramController::class, 'index'])->name('admin.showPrograms');
      Route::get('/{educationProgram}', [EducationProgramController::class, 'show'])->name('admin.showProgram');

      // Редактирование программы
      Route::get('/{educationProgram}/edit', [EducationProgramController::class, 'edit'])->name('admin.editProgram');
      Route::put('/{educationProgram}/update', [EducationProgramController::class, 'update'])->name('admin.updateProgram');

      // Удаление программы
      Route::delete('/{educationProgram}/delete', [EducationProgramController::class, 'destroy'])->name('admin.destroyProgram');

      
   });

   // Поиск студентов
   Route::get('user/search', [StudentController::class, 'search'])->name('admin.searchUser');
   
   // Выдача почт и паролей
   Route::get('/assign_emails_form', [StudentController::class, 'assignEmailsForm'])->name('admin.assignEmailsForm');
   Route::post('/assign_emails', [StudentController::class, 'assignEmails'])->name('admin.assignEmails');

   // Скачивание файла с почтами и паролями
   Route::get('/download_emails_form', [StudentController::class, 'downloadEmailsForm'])->name('admin.downloadEmailsForm');
   Route::get('/download-emails', [StudentController::class, 'downloadEmails'])->name('admin.downloadEmails');

   // Очистка email и password
   Route::get('/reset-emails-passwords', [StudentController::class, 'resetEmailsAndPasswords'])->name('admin.resetEmails');

   // Студенты
   Route::prefix('users')->group(function () {
      // Добавление студента
      Route::get('/create', [StudentController::class, 'create'])->name('admin.createUser');
      Route::post('/store', [StudentController::class, 'store'])->name('admin.storeUser');

      Route::get('/create/create_one', [StudentController::class, 'createOne'])->name('admin.createUserOne');
      Route::post('/store/store_one', [StudentController::class, 'storeOne'])->name('admin.storeUserOne');
      
      // Отображение студентов
      Route::get('/', [StudentController::class, 'index'])->name('admin.showUsers');
      Route::get('/{student}', [StudentController::class, 'show'])->name('admin.showUser');

      // Редактирование студента
      Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('admin.editUser');
      Route::put('/{student}/update', [StudentController::class, 'update'])->name('admin.updateUser');

      // Удаление студента
      Route::delete('/{student}/delete', [StudentController::class, 'destroy'])->name('admin.destroyUser');
 
   });

   // Поиск преподавателей
   Route::get('teacher/search', [TeacherController::class, 'search'])->name('admin.searchTeacher');

   // Преподаватели
   Route::prefix('teachers')->group(function () {
      // Добавление преподавателя
      Route::get('/create', [TeacherController::class, 'create'])->name('admin.createTeacher');
      Route::post('/store', [TeacherController::class, 'store'])->name('admin.storeTeacher');
      
      // Отображение преподавателей
      Route::get('/', [TeacherController::class, 'index'])->name('admin.showTeachers');
      Route::get('/{teacher}', [TeacherController::class, 'show'])->name('admin.showTeacher');

      // Редактирование преподавателя
      Route::get('/{teacher}/edit', [TeacherController::class, 'edit'])->name('admin.editTeacher');
      Route::put('/{teacher}/update', [TeacherController::class, 'update'])->name('admin.updateTeacher');

      // Удаление преподавателя
      Route::delete('/{teacher}/delete', [TeacherController::class, 'destroy'])->name('admin.destroyTeacher');

   });

   
});