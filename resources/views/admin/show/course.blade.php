@extends('layout.layout')
@section('title') 
Подробнее о курсе
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 px-4 py-4 rounded-lg">
   <div class="flex items-center gap-4 mb-8">
      <a href="{{ session('return_to', route('admin.showCourses')) }}"> <i class="fa fa-arrow-left text-2xl hover:text-gray-400 hover:border-gray-400 transition-all duration-200 border-2 rounded-full p-1"></i></a>
      <h1 class="text-2xl"><i class="fas fa-list text-2xl "></i> Курс "{{$course->name}}"</h1>
   </div>

   @if($editing ?? false)
      @if ($errors->any())
         <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
            <ul>
               @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
      @endif
      <form action="{{ route('admin.updateCourse', $course->id) }}" method="POST" class="flex flex-col gap-4">
         @csrf
         @method('PUT')
         <h1 class="flex flex-col gap-4">Название: <input type="text" name="name" value="{{ $course->name }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1 class="flex flex-col gap-4">Описание: <textarea type="text" name="description" class="text-black h-24 border-2 rounded-lg py-2 px-4 w-full md:w-1/3">{{ $course->description }}</textarea> </h1>
         <h1 class="flex flex-col gap-4">Код курса: <input type="text" name="code" value="{{ $course->code }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1 class="flex flex-col gap-4">Количество кредитов: <input type="number" min="0" step="1" name="credits" value="{{ $course->credits }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1 class="flex flex-col gap-4">Семестр: <input type="number" min="0" max="10" step="1" name="semester" value="{{ $course->semester }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1 class="flex flex-col gap-4">Тип: 
            <select name="type" id="type" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
               <option value="Обязательный" {{ $course->type == 'Обязательный' ? 'selected' : '' }}>Обязательный</option>
               <option value="Элективный" {{ $course->type == 'Элективный' ? 'selected' : '' }}>Элективный</option>
           </select>
         </h1>
         <h1 class="flex flex-col gap-4">Степень: 
            <select name="degree" id="degree" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
               <option value="Бакалавриат" {{ $course->degree == 'Бакалавриат' ? 'selected' : '' }}>Бакалавриат</option>
               <option value="Магистратура" {{ $course->degree == 'Магистратура' ? 'selected' : '' }}>Магистратура</option>
               <option value="Аспирантура" {{ $course->degree == 'Аспирантура' ? 'selected' : '' }}>Аспирантура</option>
           </select>
         </h1>
      
         <div class="mt-4 flex gap-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition cursor-pointer">
               <i class="fa fa-check text-xl"></i> Сохранить
            </button>
            <a href="{{ url()->previous() }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition cursor-pointer">
               <i class="fa fa-x text-xl"></i> Отмена
            </a>
         </div>
      </form>
   @else
      <div class="flex flex-col gap-4">
         <h1>Название: {{$course->name}}</h1>
         <h1>Описание: {{$course->description}}</h1>
         <h1>Код курса: {{$course->code}}</h1>
         <h1>Количество кредитов: {{$course->credits}}</h1>
         <h1>Семестр: {{$course->semester}}</h1>
         <h1>Тип: {{$course->type}}</h1>
         <h1>Степень: {{$course->degree}}</h1>
         <h1>Куратор курса: {{ $course->teachers->pluck('name')->join(', ') ?: 'Нет данных'}}</h1>
      </div>
      <div class="flex items-center gap-4 mt-4">
         <form action="{{ route('admin.editCourse', ['course' => $course->id]) }}" method="get">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition cursor-pointer">
               <i class="fa fa-edit text-xl"></i> Редактировать
            </button>
         </form>
         <form action="{{ route('admin.destroyCourse', ['course' => $course->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этот курс?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition cursor-pointer">
               <i class="fa fa-trash text-xl"></i> Удалить
            </button>
         </form>
      </div>
   @endif
</div>

<script src="{{ asset('js/image-preview.js') }}"></script>
<script src="{{ asset('js/toggle-pass.js') }}"></script>
@include('include.success-message')
@endsection
