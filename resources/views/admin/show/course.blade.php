@extends('layout.layout')
@section('title', 'Справка')

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <i class="fas fa-circle-info text-2xl text-blue-400"></i>
      <h1 class="text-2xl font-semibold">Справка</h1>
   </div>

   <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-blue-300"><i class="fas fa-book-open mr-2"></i>Курсы</h2>
         <p class="text-gray-300">На странице курсов вы можете создавать, редактировать и управлять учебными курсами. 
            Доступны фильтры по типу и образовательной программе.
         </p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-green-300"><i class="fas fa-users mr-2"></i>Группы</h2>
         <p class="text-gray-300">В разделе групп осуществляется добавление и распределение студентов. 
            Один студент может состоять в нескольких группах.
         </p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-yellow-300"><i class="fas fa-user-tie mr-2"></i>Преподаватели</h2>
         <p class="text-gray-300">На странице преподавателей можно добавлять новых сотрудников, а также назначать им курсы и группы.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-pink-300"><i class="fas fa-calendar-alt mr-2"></i>Расписание</h2>
         <p class="text-gray-300">Создавайте занятия, указывайте аудитории, дату, время и тип занятия. 
            Доступна поддержка повторяющихся событий.
         </p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-red-300"><i class="fas fa-envelope mr-2"></i>Почта</h2>
         <p class="text-gray-300">Система сообщений поддерживает входящие, отправленные, избранные и удалённые письма, 
            а также возможность прикрепления файлов.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-purple-300"><i class="fas fa-file-alt mr-2"></i>Отчёты</h2>
         <p class="text-gray-300">Раздел отчетности позволяет отслеживать успеваемость и выполнение заданий студентами в разрезе курсов и групп.</p>
      </div>
   </div>

   <div class="mt-10 text-sm text-gray-400 text-center">
      Если у вас возникли вопросы — обратитесь к администратору или свяжитесь с технической поддержкой.
   </div>
</div>
@endsection
