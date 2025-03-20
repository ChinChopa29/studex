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
      </div>
   </div>
@endif


@endsection