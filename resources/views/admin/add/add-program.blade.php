@extends('layout.layout')
@section('title') 
Создание образовательной программы
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{route('admin.index')}}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Создание образовательной программы
      </h1>
   </div>

   <form action="{{route('admin.storeProgram')}}" method="post" class="space-y-6">
      @csrf

      <div class="flex flex-col">
         <label for="title" class="text-lg font-medium">Название</label>
         <div class="flex items-center gap-3">
            <input type="text" id="title" name="title" placeholder="Введите название" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{old('title')}}">
            <span class="tooltip" data-tooltip="Название для образовательной программы">
               <i class="fas fa-info-circle text-xl text-gray-400"></i>
            </span>
         </div>
      </div>

      <div class="flex flex-col">
         <label for="acronym" class="text-lg font-medium">Акроним</label>
         <div class="flex items-center gap-3">
            <input type="text" id="acronym" name="acronym" placeholder="Акроним" readonly class="w-full md:w-1/3 bg-gray-300 text-black border-2 border-gray-300 rounded-lg py-2 px-4 cursor-not-allowed" value="{{old('acronym')}}">
            <span class="tooltip" data-tooltip="Акроним (сокращение) будет составлен автоматически">
               <i class="fas fa-info-circle text-xl text-gray-400"></i>
            </span>
         </div>
      </div>

      <div class="flex flex-col">
         <label for="description" class="text-lg font-medium">Описание</label>
         <textarea name="description" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 resize-none h-32 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите описание">{{ old('description') }}</textarea>
      </div>

      <div class="flex flex-col">
         <label for="degree" class="text-lg font-medium">Степень</label>
         <select id="degree" name="degree" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="Бакалавриат" @selected(old('degree') == 'Бакалавриат')>Бакалавриат</option>
            <option value="Магистратура" @selected(old('degree') == 'Магистратура')>Магистратура</option>
            <option value="Аспирантура" @selected(old('degree') == 'Аспирантура')>Аспирантура</option>
         </select>
      </div>

      <div class="flex flex-col">
         <label for="duration" class="text-lg font-medium">Длительность</label>
         <select id="duration" name="duration" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="4">4 года</option>
            <option value="3">3 года</option>
         </select>
      </div>

      <div class="flex flex-col">
         <label for="mode" class="text-lg font-medium">Форма обучения</label>
         <select name="mode" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="Очная" @selected(old('mode') == 'Очная')>Очная</option>
            <option value="Очно-заочная" @selected(old('mode') == 'Очно-заочная')>Очно-заочная</option>
            <option value="Дистанционная" @selected(old('mode') == 'Дистанционная')>Дистанционная</option>
         </select>
      </div>

      <div class="flex flex-col">
         <label for="price" class="text-lg font-medium">Стоимость</label>
         <input name="price" type="number" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Стоимость" min="0" step="0.01" value="{{old('price')}}">
      </div>

      <button class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
         <i class="fas fa-plus"></i> Создать образовательную программу
      </button>
   </form>

   @if ($errors->any())
      <div class="bg-red-500 text-white p-4 rounded-lg mt-6 shadow-lg">
         <ul class="space-y-1">
            @foreach ($errors->all() as $error)
               <li>• {{ $error }}</li>
            @endforeach
         </ul>
      </div>
   @endif
</div>

<script src="{{asset('js/add-program.js')}}"></script>
<script src="{{asset('js/alert-pop-up.js')}}"></script>
@include('include.success-message')
@endsection

