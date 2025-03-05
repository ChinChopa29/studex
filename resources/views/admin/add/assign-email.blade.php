@extends('layout.layout')
@section('title') 
Выдача почт и паролей
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{route('admin.index') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Выдача почт и паролей
      </h1>
   </div>   

      <form action="{{route('admin.assignEmails')}}" method="post">
         @csrf
         <button type="submit" class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2 mb-4">Выдать</button>
      </form>
   
      

   <h1 class="text-xl">Список студентов без почты и пароля:</h1>
   <div class="flex flex-col">
      @php
         $studentsWithoutEmail = $students->filter(fn($student) => empty($student->email));
      @endphp

      @if($studentsWithoutEmail->isEmpty())
         <h1 class="my-2">Студентов не найдено..</h1>
      @else
         @foreach($studentsWithoutEmail as $student)
            <a class="hover:underline flex items-center gap-4" href="{{ route('admin.showUser', ['student' => $student->id]) }}">
                  {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
            </a>
         @endforeach
      @endif
   </div>

   <a href="{{ route('admin.resetEmails') }}" class="w-full md:w-1/3 bg-red-500 hover:bg-red-600 transition duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2 mb-4">
      Очистить данные
   </a>

   @if (session('error'))
    <div class="bg-red-500 text-white p-4 rounded-lg mt-4">
        {{ session('error') }}
    </div>
   @endif   
</div>

@include('include.success-message')
@endsection



