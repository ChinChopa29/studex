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
<div class="text-white bg-slate-800 m-4 rounded-lg flex flex-col p-4">
   <div class="flex items-center gap-4 mb-8">
      <a href="{{route('admin.index')}}"><i class="fa fa-arrow-left text-2xl hover:text-gray-400 hover:border-gray-400 transition-all duration-200 border-2 rounded-full p-1"></i></a>
      <h1 class="text-2xl"><i class="fas fa-list text-2xl "></i> Создание курса</h1>
   </div>
   <form action="{{route('admin.storeCourse')}}" method="post" class="flex flex-col gap-4">
      @csrf
      <div class="flex items-center gap-4">
         <input type="text" name="name" placeholder="Название курса" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('name') }}" required>
         <span class="tooltip" data-tooltip="Название курса, которое будут видет преподаватели и студенты">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>
      @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex items-center gap-4">
         <textarea type="text" name="description" placeholder="Описание" 
         class="text-black border-2 bg-gray-300 rounded-lg h-24 py-2 px-4 w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('description') }}" ></textarea>
         <span class="tooltip" data-tooltip="Описание курса, необязательное поле">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>
      @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex items-center gap-4">
         <input type="number" min="0" step="1" name="credits" placeholder="Кредиты" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('credits') }}" required>
         <span class="tooltip" data-tooltip="Количество кредитов за этот курс">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>
      @error('credits') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex items-center gap-4">
         <input type="number" min="0" max="10" step="1" name="semester" placeholder="Семестр" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('semester') }}" required>
         <span class="tooltip" data-tooltip="Номер семестра, в котором будет проходить этот курс">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>

      <div class="flex flex-col gap-4">
         <label for="type" class="text-lg font-medium">Тип курса</label>
         <div class="flex items-center gap-4 ">
            <select name="type" id="type"
            class="text-black border-2 bg-gray-200 rounded-xl py-2 px-4 w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
               <option value="Обязательный">Обязательный</option>
               <option value="Элективный">Элективный</option>
            </select>
            <span class="tooltip" data-tooltip="Обязательный - будет проходиться обязательно. Элективный - курс на выбор">
               <i class="fas fa-info-circle text-2xl"></i>
            </span>
         </div>
      </div>
      @error('type') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex flex-col gap-4">
         <label for="degree" class="text-lg font-medium">Степень</label>
         <div class="flex items-center gap-4">
            <select name="degree" id="degree" class="text-black border-2 bg-gray-200 rounded-lg py-2 px-4 w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
               <option value="" disabled selected>Выберите степень</option>
               <option value="Бакалавриат">Бакалавриат</option>
               <option value="Магистратура">Магистратура</option>
               <option value="Аспирантура">Аспирантура</option>
            </select>
            <span class="tooltip" data-tooltip="Выберите академическую степень">
               <i class="fas fa-info-circle text-2xl"></i>
            </span>
         </div>
      </div>

      <div class="flex flex-col gap-4">
         <label for="education_program_id" class="text-lg font-medium">Образовательная программа</label>
         <div class="flex items-center gap-4">
            <select name="education_program_id[]" id="education_program_id" multiple 
               class="text-black border-2 bg-gray-200 rounded-lg py-2 px-4 w-full md:w-1/3 select2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
               @foreach ($educationPrograms as $educationProgram)
                  @if (!Str::endsWith($educationProgram->title, '(сокр)')) 
                     <option value="{{ $educationProgram->id }}" data-degree="{{ $educationProgram->degree }}"
                        {{ (collect(old('education_program_id'))->contains($educationProgram->id)) ? 'selected' : '' }}>
                        {{ $educationProgram->title }}
                     </option>
                  @endif
               @endforeach
            </select>
            <span class="tooltip" data-tooltip="Выберите образовательные программы, для которых предназначен курс (можно несколько)">
               <i class="fas fa-info-circle text-2xl"></i>
            </span>
         </div>
      </div>
      @error('education_program_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex items-center gap-4">
         <input type="text" name="code" placeholder="Уникальный код" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 outline-none" value="{{ old('code') }}" readonly>
         <span class="tooltip" data-tooltip="Код курса, должен быть уникальным, генерируется автоматически">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>
      <div id="code-check-result" class="text-sm mt-1"></div>
      @error('code') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <button class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
         Создать курс <i class="fas fa-plus text-lg"></i> 
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
<script src="{{asset('js/course-acronym.js')}}"></script>

<style>
   .select2-container--default .select2-selection--multiple {
      background-color: white !important; 
      color: black !important; 
      border: 2px solid #ccc; 
      border-radius: 6px;
      padding: 5px;
   }

   .select2-container--default .select2-selection--multiple .select2-selection__choice {
      background-color: #1e293b !important; 
      color: white !important; 
      border-radius: 4px;
      padding: 3px 6px;
   }

   .select2-container--default .select2-results__option {
      color: black !important; 
   }
</style>

@include('include.success-message')
@endsection