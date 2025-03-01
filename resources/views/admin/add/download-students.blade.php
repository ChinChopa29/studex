@extends('layout.layout')
@section('title') 
Выдача почт и паролей
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 rounded-lg flex flex-col p-4">
   <div class="flex items-center gap-4 mb-8">
      <a href="{{route('admin.index')}}"><i class="fa fa-arrow-left text-2xl hover:text-gray-400 hover:border-gray-400 transition-all duration-200 border-2 rounded-full p-1"></i></a>
      <h1 class="text-2xl"><i class="fas fa-list text-2xl "></i> Выдача почт и паролей</h1>
   </div>     

      <form action="{{route('admin.downloadEmails')}}" method="get" class="flex flex-col gap-4">
         @csrf
         <label for="group">Выберите группу, студентов которой Вы хотите скачать</label>
         <select name="group" id="group" class="text-black border-2 bg-gray-200 rounded-lg py-2 px-4 w-full md:w-1/3">
            @foreach ($groups as $group)
               <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach        
         </select>
         <button type="submit" class="bg-blue-600 hover:bg-blue-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg shadow-md w-full md:w-1/3 mb-4">Скачать файл с данными</button>
      </form>
  
</div>

@include('include.success-message')
@endsection



