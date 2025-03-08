@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
Курсы
@endsection


@section('content')
@if($user)
   <div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
      @include('show.course-layout')

      <div class="mt-6 p-4 rounded-xl shadow-inner">
         <h1 class="text-2xl font-bold">{{ $course->name }} ({{ $course->code }})</h1>
         <p class="mt-2 text-gray-300">Семестр проведения: {{ $course->semester }} семестр</p>
         <p class="mt-2 text-gray-300">Количество кредитов: {{ $course->credits }}</p>
         <p class="mt-2 text-gray-300">Тип курса: {{ $course->type }}</p>
         <p class="mt-2 text-gray-300">{{ $course->description }}</p>
      </div>
   </div>
@endif


@endsection