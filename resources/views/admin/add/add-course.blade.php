@extends('layout.layout')
@section('title') 
Создание курса
@endsection
@section('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{route('admin.index')}}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Создание курса
      </h1>
   </div>
   
   <form action="{{route('admin.storeCourse')}}" method="post" class="space-y-6">
      @csrf
      
      <div class="flex flex-col">
         <label for="name" class="text-lg font-medium">Название курса</label>
         <input type="text" name="name" id="name" placeholder="Введите название курса" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
      </div>
      
      <div class="flex flex-col">
         <label for="description" class="text-lg font-medium">Описание</label>
         <textarea name="description" id="description" placeholder="Введите описание" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 h-24 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
      </div>
      
      <div class="flex flex-col">
         <label for="credits" class="text-lg font-medium">Кредиты</label>
         <input type="number" name="credits" id="credits" min="0" step="1" placeholder="Введите количество кредитов" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
      </div>
      
      <div class="flex flex-col">
         <label for="semester" class="text-lg font-medium">Семестр</label>
         <input type="number" name="semester" id="semester" min="0" max="10" step="1" placeholder="Введите номер семестра" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
      </div>
      
      <div class="flex flex-col">
         <label for="type" class="text-lg font-medium">Тип курса</label>
         <select name="type" id="type" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="Обязательный">Обязательный</option>
            <option value="Элективный">Элективный</option>
         </select>
      </div>
      
      <div class="flex flex-col">
         <label for="degree" class="text-lg font-medium">Степень</label>
         <select name="degree" id="degree" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="Бакалавриат">Бакалавриат</option>
            <option value="Магистратура">Магистратура</option>
            <option value="Аспирантура">Аспирантура</option>
         </select>
      </div>
      
      <div class="flex flex-col">
         <label for="education_program_id" class="text-lg font-medium">Образовательная программа</label>
         <select name="education_program_id[]" id="education_program_id" multiple 
            class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 select2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            @foreach ($educationPrograms as $educationProgram)
               <option value="{{ $educationProgram->id }}">{{ $educationProgram->title }}</option>
            @endforeach
         </select>
      </div>
      
      <button class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
         <i class="fas fa-plus"></i> Создать курс
      </button>
   </form>
</div>

<script>
   $(document).ready(function () {
      $("#education_program_id").select2({
         placeholder: "Выберите программы",
         allowClear: true
      });
   });
</script>
@include('include.success-message')
@endsection