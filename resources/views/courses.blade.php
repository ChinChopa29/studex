@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
Курсы
@endsection


@section('content')
<div class="flex text-white m-4 ">
   <div class="bg-gradient-to-r from-blue-600 to-purple-600 w-full p-6 rounded-2xl shadow-lg">
      <h1 class="font-bold text-xl">Добро пожаловать в STUDEX!</h1>
      <h2>Ваша платформа для онлайн обучения</h2>
   </div>
</div>
@if($user) 
   <div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
         @forelse ($courses as $course)
            @if($user instanceof \App\Models\Student ? $user->courses->contains($course) : true)
               <a href="{{ $user instanceof \App\Models\Student ? route('studentCourseShow', ['course' => $course->id]) : route('teacherCourseShow', ['course' => $course->id] )}}" 
                  class="relative bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-2xl shadow-lg transition-transform transform hover:scale-105 cursor-pointer">
                     <h2 class="text-xl font-bold mb-2">{{ $course->name }}</h2>
                     
                     <button class="absolute top-4 right-4 text-white hover:text-gray-200 transition">
                        <i class="fas fa-cog text-xl"></i>
                     </button>
               </a>
            @endif
         @empty
            <p class="text-gray-600">У вас нет назначенных курсов.</p>
         @endforelse
      </div>
   </div>
@endif
@endsection