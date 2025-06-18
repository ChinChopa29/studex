@extends('layout.layout')
@section('title') 
Добавление кабинета
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
      <h1 class="text-2xl"><i class="fas fa-list text-2xl "></i> Добавление кабинета</h1>
   </div>
   <form action="{{ route('admin.storeClassroom') }}" method="post" class="flex flex-col gap-4">
      @csrf

      <div class="flex items-center gap-4">
         <input type="text" name="number" placeholder="Номер кабинета" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4" value="{{ old('number') }}" required>
         <span class="tooltip" data-tooltip="Например: 720, 2.101, A-207">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>
      @error('number') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex items-center gap-4">
         <input type="number" name="capacity" min="1" placeholder="Вместимость" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4" value="{{ old('capacity') }}" required>
         <span class="tooltip" data-tooltip="Количество мест в кабинете">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>
      @error('capacity') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex items-center gap-4">
         <select name="type" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4" required>
            <option value="" disabled selected>Выберите тип кабинета</option>
            <option value="Обычный">Обычный</option>
            <option value="Компьютерный">Компьютерный</option>
            <option value="Лаборатория">Лаборатория</option>
            <option value="Актовый зал">Актовый зал</option>
         </select>
         <span class="tooltip" data-tooltip="Тип аудитории (например, компьютерный)">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>
      @error('type') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex items-center gap-4">
         <input type="number" name="computers" min="0" placeholder="Количество компьютеров (если есть)" 
         class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4" value="{{ old('computers') }}">
         <span class="tooltip" data-tooltip="Оставьте пустым, если это не компьютерный кабинет">
            <i class="fas fa-info-circle text-2xl"></i>
         </span>
      </div>
      @error('computers') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

      <div class="flex flex-col gap-2">
         <label for="education_programs" class="text-lg font-medium">Образовательные программы</label>
         <select name="education_program_ids[]" id="education_program_ids" multiple
            class="select2 w-full md:w-1/2 text-black border-2 border-gray-300 bg-white rounded-lg py-2 px-4">
            @foreach($educationPrograms as $program)
               <option value="{{ $program->id }}">{{ $program->title }}</option>
            @endforeach
         </select>
         @error('education_programs') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
      </div>

      <button class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
         Создать кабинет <i class="fas fa-plus text-lg"></i> 
      </button>
   </form>
</div>

<script>
   $(document).ready(function() {
      $('.select2').select2({
         placeholder: "Выберите программы",
         width: '100%',
      });
   });
</script>

@include('include.success-message')
@endsection
