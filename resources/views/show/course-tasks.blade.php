@extends('layout.layout')
@section('title') 
    Доступные задания
@endsection

@section('content')
@if(Auth::guard('teacher')->check())
   <div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
      @include('show.course-layout')

      <div class="mt-6 p-4 rounded-xl shadow-inner">
         <h1 class="text-2xl font-bold">{{ $course->name }} ({{ $course->code }})</h1>
      </div>

      <div class="mt-6 border-b border-gray-600 flex">
         <button class="tab-btn px-4 py-2" data-tab="available">Доступные</button>
         <button class="tab-btn px-4 py-2" data-tab="upcoming">Предстоящие</button>
         <button class="tab-btn px-4 py-2" data-tab="completed">Завершенные</button>
      </div>

      <div id="available" class="tab-content mt-4">
         <h1 class="text-xl font-bold p-4"><i class="fa fa-check-circle text-green-500"></i> Доступные задания</h1>
         @forelse ($tasks as $task)
            @php
               $totalTime = \Carbon\Carbon::parse($task->deadline)->diffInSeconds($task->from);
               $remainingTime = \Carbon\Carbon::parse($task->deadline)->diffInSeconds(now());
               $progress = 100 - ($remainingTime / $totalTime * 100);
               $progress = max(0, min($progress, 100)); 
            @endphp
            @if($task->from <= now() && $task->deadline > now()) 
               <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}">
                  <div class="task-item p-4 border border-slate-700 hover:bg-slate-700 transition-all duration-200 rounded-lg">
                     <h1 class="font-bold">{{ $loop->iteration }}. {{ $task->name }}</h1>
                     <div class="text-slate-500 text-sm">Доступно до: {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('j F Y года') }}</div>
                     <div class="w-full bg-gray-700 h-2 rounded-full mt-2 relative">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $progress }}%;"></div>
                        <span class="absolute top-[-20px] right-0 text-xs text-gray-400">{{ round($progress) }}%</span>
                     </div>
                     <div class="text-xs text-gray-400 mt-1">Прогресс истечения срока</div>
                  </div>
               </a>
            @endif
         @empty
            <h1 class="text-xl font-bold p-4">Заданий еще нет</h1> 
         @endforelse
      </div>

      <div id="upcoming" class="tab-content mt-4 hidden">
         <h1 class="text-xl font-bold p-4"><i class="fa fa-clock text-yellow-500"></i> Предстоящие задания</h1>
         @forelse ($tasks as $task)
            @if($task->from >= now() && $task->deadline > now()) 
               <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}">
                  <div class="task-item p-4 border border-slate-700 hover:bg-slate-700 transition-all duration-200 rounded-lg">
                     <h1 class="font-bold">{{ $loop->iteration }}. {{ $task->name }}</h1>
                     <div class="text-slate-500 text-sm">Откроется: {{ \Carbon\Carbon::parse($task->from)->translatedFormat('j F Y года') }}</div>
                  </div>
               </a>
            @endif
         @empty
            <h1 class="text-xl font-bold p-4">Заданий еще нет</h1> 
         @endforelse
      </div>

      <div id="completed" class="tab-content mt-4 hidden">
         <h1 class="text-xl font-bold p-4"><i class="fa fa-times-circle text-red-500"></i> Завершенные задания</h1>
         @forelse ($tasks as $task)
            @if($task->from <= now() && $task->deadline < now()) 
               <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}">
                  <div class="task-item p-4 border border-slate-700 hover:bg-slate-700 transition-all duration-200 rounded-lg">
                     <h1 class="font-bold">{{ $loop->iteration }}. {{ $task->name }}</h1>
                     <div class="text-slate-500 text-sm">Завершилось: {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('j F Y года') }}</div>
                  </div>
               </a>
            @endif
         @empty
            <h1 class="text-xl font-bold p-4">Заданий еще нет</h1> 
         @endforelse
      </div>

   </div>
@endif

<script>
   document.addEventListener("DOMContentLoaded", function () {
       const tabs = document.querySelectorAll(".tab-btn");
       const contents = document.querySelectorAll(".tab-content");

       tabs.forEach(tab => {
           tab.addEventListener("click", function () {
               tabs.forEach(t => t.classList.remove("border-b-2", "border-blue-500", "text-blue-500"));
               contents.forEach(c => c.classList.add("hidden"));

               tab.classList.add("border-b-2", "border-blue-500", "text-blue-500");
               document.getElementById(tab.dataset.tab).classList.remove("hidden");
           });
       });

       tabs[0].classList.add("border-b-2", "border-blue-500", "text-blue-500");
   });
</script>

<style>
   .task-item {
      transition: transform 0.2s ease-in-out;
   }
   .task-item:hover {
      transform: scale(1.02);
   }
</style>

@endsection
