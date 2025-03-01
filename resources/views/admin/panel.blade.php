@extends('layout.layout')
@section('title') 
Админ панель
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 p-6 rounded-lg shadow-lg">
   <h1 class="text-3xl font-bold mb-4">Панель администратора</h1>
   <p class="text-gray-400 mb-8">Здесь вы можете управлять группами, курсами, студентами и преподавателями.</p>

   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Создание сущностей -->
      <div class="bg-slate-900 shadow-lg rounded-lg p-6 transition transform hover:scale-105 hover:shadow-xl">
         <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-plus-circle text-2xl"></i> Создание сущностей
         </h2>
         <div class="space-y-4">
            <a href="{{route('admin.createGroup')}}" class="flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-500 transition">
               <i class="fas fa-users text-xl"></i> Создать группу
            </a>
            <a href="{{route('admin.createCourse')}}" class="flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-500 transition">
               <i class="fas fa-book text-xl"></i> Создать курс
            </a>
            <a href="{{route('admin.createProgram')}}" class="flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-500 transition">
               <i class="fas fa-pie-chart text-xl"></i> Создать программу обучения
            </a>
         </div>
      </div>

      <!-- Управление сущностями -->
      <div class="bg-slate-900 shadow-lg rounded-lg p-6 transition transform hover:scale-105 hover:shadow-xl">
         <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-cogs text-2xl"></i> Управление сущностями
         </h2>
         <div class="space-y-4">
            <a href="{{route('admin.showGroups')}}" class="flex items-center gap-2 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500 transition">
               <i class="fas fa-list text-xl"></i> Группы
            </a>
            <a href="{{route('admin.showCourses')}}" class="flex items-center gap-2 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500 transition">
               <i class="fas fa-list text-xl"></i> Курсы
            </a>
            <a href="{{route('admin.showPrograms')}}" class="flex items-center gap-2 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500 transition">
               <i class="fas fa-list text-xl"></i> Образовательные программы
            </a>
         </div>
      </div>
   </div>

   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-8">
      <!-- Создание пользователей -->
      <div class="bg-slate-900 shadow-lg rounded-lg p-6 transition transform hover:scale-105 hover:shadow-xl">
         <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-user-plus text-2xl"></i> Создание пользователей
         </h2>
         <div class="space-y-4">
            <a href="{{route('admin.createUser')}}" class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 transition">
               <i class="fas fa-user-plus text-xl"></i> Добавить студентов
            </a>
            <a href="{{route('admin.createTeacher')}}" class="flex items-center gap-2 bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-500 transition">
               <i class="fas fa-id-card text-xl"></i> Добавить преподавателя
            </a>
         </div>
      </div>
      
      <!-- Управление пользователями -->
      <div class="bg-slate-900 shadow-lg rounded-lg p-6 transition transform hover:scale-105 hover:shadow-xl">
         <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-users text-2xl"></i> Управление пользователями
         </h2>
         <div class="space-y-4">
            <a href="{{route('admin.showUsers')}}" class="flex items-center gap-2 bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-500 transition">
               <i class="fas fa-list text-xl"></i> Список студентов
            </a>
            <a href="{{route('admin.showTeachers')}}" class="flex items-center gap-2 bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-500 transition">
               <i class="fas fa-list text-xl"></i> Список преподавателей
            </a>
         </div>
      </div>
   </div>

   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-8">
      <!-- Дополнительно -->
      <div class="bg-slate-900 shadow-lg rounded-lg p-6 transition transform hover:scale-105 hover:shadow-xl">
         <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-dashboard text-2xl"></i> Дополнительно
         </h2>
         <div class="space-y-4">
            <a href="{{route('admin.assignEmailsForm')}}" class="flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-500 transition">
               <i class="fas fa-gears text-xl"></i> Выдача почт и паролей
            </a>
            <a href="{{route('admin.downloadEmailsForm')}}" class="flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-500 transition">
               <i class="fas fa-file-excel text-xl"></i> Скачать список студентов
            </a>
         </div>
      </div>
   </div>
</div>
@endsection
