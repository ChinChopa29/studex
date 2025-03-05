@extends('layout.layout')
@section('title') 
Скачать список студентов
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{route('admin.index') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Скачать список студентов
      </h1>
   </div>     

      <form action="{{route('admin.downloadEmails')}}" method="get" class="flex flex-col gap-4">
         @csrf
         <label for="group" class="text-lg font-medium">Выберите группу, студентов которой Вы хотите скачать</label>
         <select name="group" id="group" class="text-black border-2 bg-gray-200 rounded-lg py-2 px-4 w-full md:w-1/3">
            @foreach ($groups as $group)
               <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach        
         </select>
         <button type="submit" class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2 mb-4">Скачать файл с данными</button>
      </form>
  
</div>

@include('include.success-message')
@endsection



