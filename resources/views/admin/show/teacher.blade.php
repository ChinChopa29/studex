@extends('layout.layout')
@section('title') 
Профиль преподавателя
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 px-4 py-4 rounded-lg">
   <div class="flex items-center gap-4 mb-8">
      <a href="{{ session('return_to', route('admin.showTeachers')) }}"> <i class="fa fa-arrow-left text-2xl hover:text-gray-400 hover:border-gray-400 transition-all duration-200 border-2 rounded-full p-1"></i></a>
      <h1 class="text-2xl"><i class="fas fa-list text-2xl "></i> Преподаватель {{$teacher->name}} {{$teacher->lastname}}</h1>
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
      <form action="{{ route('admin.updateTeacher', $teacher->id) }}" method="POST" class="flex flex-col gap-4">
         @csrf
         @method('PUT')
         <img src="{{ asset('storage/' . $teacher->image) }}" alt="Фото преподавателя" class="w-64 h-64 object-cover rounded-lg">
         <label for="image" class="bg-green-600 py-2 px-4 rounded-lg hover:bg-green-700 transition-all duration-200 cursor-pointer w-full md:w-1/3">
            <i class="fa fa-image text-xl"></i> Загрузить изображение
         </label>
         <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" class="hidden">
         <div id="imagePreviewContainer" class="mt-2 hidden">
            <img id="imagePreview" class="rounded-lg border-2 border-gray-300 w-32 h-32 object-cover">
            <button type="button" onclick="clearImage()" class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition cursor-pointer">Удалить</button>
         </div>
         <h1 class="flex flex-col gap-4">ФИО:
             <input type="text" name="name" value="{{ $teacher->name }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
             <input type="text" name="surname" value="{{ $teacher->surname }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
             <input type="text" name="lastname" value="{{ $teacher->lastname }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
         </h1>
         <h1>ИИН: <input type="text" name="iin" value="{{ $teacher->iin }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Телефон: <input type="text" name="phone" value="{{ $teacher->phone }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Пол: 
            <select name="gender" id="gender" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
               <option value="Мужской" {{ $teacher->gender == 'Мужской' ? 'selected' : '' }}>Мужской</option>
               <option value="Женский" {{ $teacher->gender == 'Женский' ? 'selected' : '' }}>Женский</option>
           </select>
         </h1>
         <h1>Дата рождения: <input type="date" name="birthday" value="{{ $teacher->birthday }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Корпоративная почта: <input type="email" name="email" value="{{ $teacher->email }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1 class="flex flex-col gap-4">Пароль: 
            <div class="relative">
               <input type="password" id="password" name="password" value="{{ $teacher->password }}" 
                  class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3 pr-10">
               <button type="button" onclick="togglePassword()" 
                  class="absolute inset-y-0 right-0 flex items-center px-3">
                  <i id="eyeIcon" class="fa fa-eye text-gray-500"></i>
               </button>
            </div>
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
         <img src="{{ asset('storage/' . $teacher->image) }}" alt="Фото преподавателя" class="w-64 h-64 object-cover rounded-lg">
         <h1>ФИО: {{$teacher->name}} {{$teacher->surname}} {{$teacher->lastname}}</h1>
         <h1>ИИН: {{$teacher->iin}}</h1>
         <h1>Телефон: {{$teacher->phone}}</h1>
         <h1>Пол: {{$teacher->gender}}</h1>
         <h1>Дата рождения: {{$teacher->birthday}}</h1>
         <h1>Группа: {{ $teacher->groups->pluck('name')->join(', ') ?: 'Нет данных'}}</h1>
         {{-- <h1>Курсы: {{ $teacher->courses->pluck('name')->join(', ') ?: 'Нет данных'}}</h1> --}}
         <h1>Почта: {{ $teacher->email ?: 'Нет данных'}}</h1>
      </div>
      <div class="flex items-center gap-4 mt-4">
         <form action="{{ route('admin.editTeacher', ['teacher' => $teacher->id]) }}" method="get">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition cursor-pointer">
               <i class="fa fa-edit text-xl"></i> Редактировать
            </button>
         </form>
         <form action="{{ route('admin.destroyTeacher', ['teacher' => $teacher->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этого преподавателя?');">
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
