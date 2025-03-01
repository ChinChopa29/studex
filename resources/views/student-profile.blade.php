@extends('layout.layout')
@section('title') 
Профиль студента
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 px-4 py-4 rounded-lg">
      <div class="flex flex-col gap-4">
         <h1>ФИО: {{$student->name}} {{$student->surname}} {{$student->lastname}}</h1>
         <h1>ИИН: {{$student->iin}}</h1>
         <h1>Телефон: {{$student->phone}}</h1>
         <h1>Пол: {{$student->gender}}</h1>
         <h1>Дата рождения: {{$student->birthday}}</h1>
         <h1>Год поступления: {{$student->admission_year}}</h1>
         <h1>Год окончания: {{$student->graduation_year}}</h1>
         <h1>Группа: {{ $student->groups->pluck('name')->join(', ') ?: 'Нет данных'}}</h1>
         <h1>Почта: {{ $student->email ?: 'Нет данных'}}</h1>
         <h1>Образовательная программа: {{ $student->educationProgram->title}}</h1>
      </div>
</div>


<script src="{{ asset('js/alert-pop-up.js') }}"></script>

@include('include.success-message')
@endsection
