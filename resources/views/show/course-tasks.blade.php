@extends('layout.layout')
@section('title') 
    Доступные задания
@endsection

@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Хлебные крошки и заголовок -->
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex') }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseShow', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">{{$course->name}}</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">Задания</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseShow', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">{{ $course->name }} <span class="text-gray-400">({{ $course->code }})</span></h1>
                </div>
               
               @if(Auth::guard('teacher')->check())
               <a href="{{ route('teacherCourseCreateTask', ['course' => $course->id]) }}" 
                  class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                   <i class="fas fa-plus mr-2"></i> Добавить задание
               </a>
               @endif
           </div>
       </div>

       <!-- Табы -->
       <div class="border-b border-gray-700 mb-6">
           <div class="flex space-x-4">
               <button class="tab-btn px-4 py-3 font-medium text-gray-400 hover:text-white border-b-2 border-transparent hover:border-blue-500 transition-all duration-200" data-tab="available">
                   <i class="fas fa-check-circle mr-2"></i> Доступные
               </button>
               <button class="tab-btn px-4 py-3 font-medium text-gray-400 hover:text-white border-b-2 border-transparent hover:border-blue-500 transition-all duration-200" data-tab="upcoming">
                   <i class="fas fa-clock mr-2"></i> Предстоящие
               </button>
               <button class="tab-btn px-4 py-3 font-medium text-gray-400 hover:text-white border-b-2 border-transparent hover:border-blue-500 transition-all duration-200" data-tab="completed">
                   <i class="fas fa-times-circle mr-2"></i> Завершенные
               </button>
           </div>
       </div>

       <!-- Контент табов -->
       <div class="space-y-6">
           <!-- Доступные задания -->
           <div id="available" class="tab-content">
               @php
                   $availableTasks = $tasks->filter(fn($task) => $task->from <= now() && $task->deadline > now());
               @endphp
               
               @if ($availableTasks->isNotEmpty())
                   <div class="grid grid-cols-1 gap-4">
                       @foreach ($availableTasks as $task)
                           @php
                               $totalTime = \Carbon\Carbon::parse($task->deadline)->diffInSeconds($task->from);
                               $remainingTime = \Carbon\Carbon::parse($task->deadline)->diffInSeconds(now());
                               $progress = 100 - ($remainingTime / $totalTime * 100);
                               $progress = max(0, min($progress, 100));
                               
                               // Получаем статус студента для этого задания
                               $submission = $task->studentFiles->where('student_id', Auth::id())->first();
                               $grade = $task->grades->where('student_id', Auth::id())->first();
                           @endphp
                           
                           <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}" class="group">
                               <div class="bg-gray-800 p-5 rounded-xl hover:bg-gray-700 transition-colors duration-200 shadow-lg">
                                   <div class="flex justify-between items-start">
                                       <div>
                                           <h3 class="text-xl font-semibold group-hover:text-blue-400 transition-colors duration-200">
                                               {{ $loop->iteration }}. {{ $task->name }}
                                           </h3>
                                           <p class="text-gray-400 mt-1">
                                               Доступно до: {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('j F Y года') }}
                                           </p>
                                       </div>
                                       <span class="text-sm bg-blue-900 text-blue-300 px-3 py-1 rounded-full">
                                           {{ round($progress) }}%
                                       </span>
                                   </div>
                                   
                                   <div class="mt-3">
                                       <div class="w-full bg-gray-700 h-2 rounded-full">
                                           <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $progress }}%;"></div>
                                       </div>
                                       <div class="text-xs text-gray-500 mt-1">Прогресс истечения срока</div>
                                   </div>

                                   <!-- Статус студента -->
                                   @if(Auth::guard('student')->check())
                                   <div class="mt-4 pt-3 border-t border-gray-700">
                                       @if($submission)
                                           @if($grade)
                                               <div class="flex items-center text-green-400">
                                                   <i class="fas fa-check-circle mr-2"></i>
                                                   <span>Ваша оценка: {{ $grade->grade }}/100</span>
                                               </div>
                                           @else
                                               <div class="flex items-center text-yellow-400">
                                                   <i class="fas fa-hourglass-half mr-2"></i>
                                                   <span>Ожидание проверки</span>
                                               </div>
                                           @endif
                                       @else
                                           <div class="flex items-center text-red-400">
                                               <i class="fas fa-exclamation-circle mr-2"></i>
                                               <span>Вы еще не сдали это задание</span>
                                           </div>
                                       @endif
                                   </div>
                                   @endif
                               </div>
                           </a>
                       @endforeach
                   </div>
               @else
                   <div class="bg-gray-800 p-8 rounded-xl text-center">
                       <i class="fas fa-tasks text-4xl text-gray-600 mb-4"></i>
                       <h3 class="text-xl font-semibold">Нет доступных заданий</h3>
                       <p class="text-gray-400 mt-2">Посмотрите предстоящие или завершенные задания</p>
                   </div>
               @endif
           </div>

           <!-- Предстоящие задания -->
           <div id="upcoming" class="tab-content hidden">
               @php
                   $upcomingTasks = $tasks->filter(fn($task) => $task->from > now());
               @endphp
               
               @if ($upcomingTasks->isNotEmpty())
                   <div class="grid grid-cols-1 gap-4">
                       @foreach ($upcomingTasks as $task)
                           <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}" class="group">
                               <div class="bg-gray-800 p-5 rounded-xl hover:bg-gray-700 transition-colors duration-200 shadow-lg">
                                   <div class="flex justify-between items-start">
                                       <div>
                                           <h3 class="text-xl font-semibold group-hover:text-yellow-400 transition-colors duration-200">
                                               {{ $loop->iteration }}. {{ $task->name }}
                                           </h3>
                                           <p class="text-gray-400 mt-1">
                                               Откроется: {{ \Carbon\Carbon::parse($task->from)->translatedFormat('j F Y года') }}
                                           </p>
                                       </div>
                                       <span class="text-sm bg-yellow-900 text-yellow-300 px-3 py-1 rounded-full">
                                           <i class="fas fa-clock mr-1"></i> Скоро
                                       </span>
                                   </div>

                                   <!-- Статус студента -->
                                   @if(Auth::guard('student')->check())
                                   <div class="mt-4 pt-3 border-t border-gray-700 text-gray-400">
                                       <i class="fas fa-lock mr-2"></i>
                                       <span>Задание еще не доступно</span>
                                   </div>
                                   @endif
                               </div>
                           </a>
                       @endforeach
                   </div>
               @else
                   <div class="bg-gray-800 p-8 rounded-xl text-center">
                       <i class="fas fa-calendar-times text-4xl text-gray-600 mb-4"></i>
                       <h3 class="text-xl font-semibold">Нет предстоящих заданий</h3>
                       <p class="text-gray-400 mt-2">Все задания уже доступны или завершены</p>
                   </div>
               @endif
           </div>

           <!-- Завершенные задания -->
           <div id="completed" class="tab-content hidden">
               @php
                   $completedTasks = $tasks->filter(fn($task) => $task->deadline < now());
               @endphp
               
               @if ($completedTasks->isNotEmpty())
                   <div class="grid grid-cols-1 gap-4">
                       @foreach ($completedTasks as $task)
                           @php
                               // Для завершенных заданий также получаем статус
                               $submission = $task->studentFiles->where('student_id', Auth::id())->first();
                               $grade = $task->grades->where('student_id', Auth::id())->first();
                           @endphp
                           
                           <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}" class="group">
                               <div class="bg-gray-800 p-5 rounded-xl hover:bg-gray-700 transition-colors duration-200 shadow-lg">
                                   <div class="flex justify-between items-start">
                                       <div>
                                           <h3 class="text-xl font-semibold group-hover:text-red-400 transition-colors duration-200">
                                               {{ $loop->iteration }}. {{ $task->name }}
                                           </h3>
                                           <p class="text-gray-400 mt-1">
                                               Завершено: {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('j F Y года') }}
                                           </p>
                                       </div>
                                       <span class="text-sm bg-red-900 text-red-300 px-3 py-1 rounded-full">
                                           <i class="fas fa-lock mr-1"></i> Завершено
                                       </span>
                                   </div>

                                   <!-- Статус студента -->
                                   @if(Auth::guard('student')->check())
                                   <div class="mt-4 pt-3 border-t border-gray-700">
                                       @if($submission)
                                           @if($grade)
                                               <div class="flex items-center {{ $grade->grade >= 60 ? 'text-green-400' : 'text-red-400' }}">
                                                   <i class="fas fa-check-circle mr-2"></i>
                                                   <span>Ваша оценка: {{ $grade->grade }}/100</span>
                                               </div>
                                           @else
                                               <div class="flex items-center text-yellow-400">
                                                   <i class="fas fa-hourglass-half mr-2"></i>
                                                   <span>Ожидание проверки</span>
                                               </div>
                                           @endif
                                       @else
                                           <div class="flex items-center text-red-400">
                                               <i class="fas fa-times-circle mr-2"></i>
                                               <span>Вы не сдали это задание</span>
                                           </div>
                                       @endif
                                   </div>
                                   @endif
                               </div>
                           </a>
                       @endforeach
                   </div>
               @else
                   <div class="bg-gray-800 p-8 rounded-xl text-center">
                       <i class="fas fa-check-circle text-4xl text-gray-600 mb-4"></i>
                       <h3 class="text-xl font-semibold">Нет завершенных заданий</h3>
                       <p class="text-gray-400 mt-2">Все задания еще активны или предстоят</p>
                   </div>
               @endif
           </div>
       </div>
   </div>
</div>
@endif

<script>
  document.addEventListener("DOMContentLoaded", function () {
      const tabs = document.querySelectorAll(".tab-btn");
      const contents = document.querySelectorAll(".tab-content");

      tabs.forEach(tab => {
          tab.addEventListener("click", function () {
              tabs.forEach(t => {
                  t.classList.remove("border-blue-500", "text-white");
                  t.classList.add("text-gray-400", "border-transparent");
              });
              contents.forEach(c => c.classList.add("hidden"));

              this.classList.remove("text-gray-400", "border-transparent");
              this.classList.add("border-blue-500", "text-white");
              document.getElementById(this.dataset.tab).classList.remove("hidden");
          });
      });

      // Активируем первый таб по умолчанию
      tabs[0].click();
  });
</script>

@include('include.success-message')
@include('include.error-message') 
@endsection