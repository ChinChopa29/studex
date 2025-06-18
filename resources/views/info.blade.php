@extends('layout.layout')
@section('title', 'Справка')

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <i class="fas fa-circle-info text-2xl text-blue-400"></i>
      <h1 class="text-2xl font-semibold">Справка по системе</h1>
   </div>

   <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-blue-300"><i class="fas fa-user-circle mr-2"></i>Аккаунт</h2>
         <p class="text-gray-300">В вашем профиле вы можете сменить пароль. Здесь же отображается ваша роль в системе и основная контактная информация.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-green-300"><i class="fas fa-book-open mr-2"></i>Курсы</h2>
         <p class="text-gray-300">Раздел содержит список всех доступных вам курсов. Каждый курс включает в себя несколько функциональных разделов.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-yellow-300"><i class="fas fa-calendar-day mr-2"></i>Расписание курса</h2>
         <p class="text-gray-300">Календарь занятий курса с указанием даты, времени, аудитории и типа занятия. Поддерживается просмотр по неделям и месяцам, с выделением различных типов занятий.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-purple-300"><i class="fas fa-tasks mr-2"></i>Задания курса</h2>
         <p class="text-gray-300">Список всех заданий курса с возможностью фильтрации по рубежным контролям. Для преподавателей - инструменты для создания и проверки работ. Для студентов - возможность загрузки выполненных заданий.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-red-300"><i class="fas fa-chart-bar mr-2"></i>Отчетность</h2>
         <p class="text-gray-300">Преподаватели могут просматривать и анализировать успеваемость студентов: посещаемость, выполнение заданий, текущие оценки. Доступны различные формы отчетов и статистики.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-pink-300"><i class="fas fa-users-cog mr-2"></i>Управление группами</h2>
         <p class="text-gray-300">Инструменты для преподавателей по управлению группами на курсе: просмотр состава, принятие/отклонение заявок, приглашение новых групп.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-indigo-300"><i class="fas fa-users mr-2"></i>Группы</h2>
         <p class="text-gray-300">Полный список всех учебных групп учреждения.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-teal-300"><i class="fas fa-envelope mr-2"></i>Почта</h2>
         <p class="text-gray-300">Полноценная почтовая система с возможностью отправки и получения сообщений внутри платформы, удаления и добавления в избранное.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-orange-300"><i class="fas fa-file-upload mr-2"></i>Сдача работ</h2>
         <p class="text-gray-300">Студенты могут загружать выполненные задания, прикреплять файлы и оставлять комментарии. Система автоматически фиксирует время отправки и преподаватель должен выставить оценку.</p>
      </div>

      <div class="bg-slate-700 p-5 rounded-xl shadow-md hover:shadow-xl transition duration-300">
         <h2 class="text-xl font-semibold mb-2 text-amber-300"><i class="fas fa-question-circle mr-2"></i>Тесты</h2>
         <p class="text-gray-300">Преподаватели могут создавать тесты с автоматической проверкой. Студенты видят доступные тесты, сроки выполнения и свои результаты после завершения.</p>
      </div>
   </div>

   <div class="mt-10 text-sm text-gray-400 text-center">
      Для получения дополнительной помощи обратитесь к администратору системы или в <a href="#" class="">техническую поддержку.</a>
   </div>
</div>
@endsection