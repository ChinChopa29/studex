@extends('layout.layout')
@section('title') 
Профиль преподавателя
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{ route('admin.showTeachers') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Преподаватель {{ $teacher->name }} {{ $teacher->lastname }}
      </h1>
   </div>

   @if($editing ?? false)
      @if ($errors->any())
         <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg mb-6">
            <ul class="space-y-1">
               @foreach ($errors->all() as $error)
                  <li>• {{ $error }}</li>
               @endforeach
            </ul>
         </div>
      @endif
      <form action="{{ route('admin.updateTeacher', $teacher->id) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
         @csrf
         @method('PUT')

         <!-- Изображение -->
         <div class="flex flex-col">
            <label class="text-lg font-medium">Фото преподавателя</label>
            <img src="{{ asset('storage/' . $teacher->image) }}" alt="Фото преподавателя" class="w-64 h-64 object-cover rounded-lg">
            <label for="image" class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2 cursor-pointer mt-4">
               <i class="fas fa-image"></i> Загрузить изображение
            </label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" class="hidden">
            <div id="imagePreviewContainer" class="mt-2 hidden">
               <img id="imagePreview" class="rounded-lg border-2 border-gray-300 w-32 h-32 object-cover">
               <button type="button" onclick="clearImage()" class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition cursor-pointer">Удалить</button>
            </div>
         </div>

         <!-- ФИО -->
         <div class="flex flex-col">
            <label class="text-lg font-medium">ФИО</label>
            <div class="flex flex-col gap-4">
               <input type="text" name="name" value="{{ $teacher->name }}"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <input type="text" name="surname" value="{{ $teacher->surname }}"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <input type="text" name="lastname" value="{{ $teacher->lastname }}"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
         </div>

         <!-- ИИН -->
         <div class="flex flex-col">
            <label for="iin" class="text-lg font-medium">ИИН</label>
            <input type="text" id="iin" name="iin" value="{{ $teacher->iin }}"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>

         <!-- Телефон -->
         <div class="flex flex-col">
            <label for="phone" class="text-lg font-medium">Телефон</label>
            <input type="text" id="phone" name="phone" value="{{ $teacher->phone }}"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>

         <!-- Пол -->
         <div class="flex flex-col">
            <label for="gender" class="text-lg font-medium">Пол</label>
            <select id="gender" name="gender"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <option value="Мужской" {{ $teacher->gender == 'Мужской' ? 'selected' : '' }}>Мужской</option>
               <option value="Женский" {{ $teacher->gender == 'Женский' ? 'selected' : '' }}>Женский</option>
            </select>
         </div>

         <!-- Дата рождения -->
         <div class="flex flex-col">
            <label for="birthday" class="text-lg font-medium">Дата рождения</label>
            <input type="date" id="birthday" name="birthday" value="{{ $teacher->birthday }}"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>

         <!-- Корпоративная почта -->
         <div class="flex flex-col">
            <label for="email" class="text-lg font-medium">Корпоративная почта</label>
            <input type="email" id="email" name="email" value="{{ $teacher->email }}"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>

         <!-- Пароль -->
         <div class="flex flex-col">
            <label for="password" class="text-lg font-medium">Пароль</label>
            <div class="relative w-full md:w-1/3">
               <input type="password" id="password" name="password"
                  class="w-full bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <button type="button" onclick="togglePassword()"
                  class="absolute inset-y-0 right-3 flex items-center">
                  <i id="eyeIcon" class="fa fa-eye text-gray-500"></i>
               </button>
            </div>
         </div>

         <!-- Кнопки -->
         <div class="flex flex-wrap gap-4">
            <button type="submit" class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-check"></i> Сохранить
            </button>
            <a href="{{ url()->previous() }}" class="w-full md:w-1/3 bg-red-500 hover:bg-red-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-x"></i> Отмена
            </a>
         </div>
      </form>
   @else
      <div class="space-y-4">
         <div class="flex flex-col">
            <label class="text-lg font-medium">Фото преподавателя</label>
            <img src="{{ asset('storage/' . $teacher->image) }}" alt="Фото преподавателя" class="w-64 h-64 object-cover rounded-lg">
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">ФИО</label>
            <p class="text-gray-300">{{ $teacher->name }} {{ $teacher->surname }} {{ $teacher->lastname }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">ИИН</label>
            <p class="text-gray-300">{{ $teacher->iin }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Телефон</label>
            <p class="text-gray-300">{{ $teacher->phone }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Пол</label>
            <p class="text-gray-300">{{ $teacher->gender }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Дата рождения</label>
            <p class="text-gray-300">
               {{ \Carbon\Carbon::parse($teacher->birthday)->translatedFormat('j F Y года') }}
           </p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Группа</label>
            <p class="text-gray-300">{{ $teacher->groups->pluck('name')->join(', ') ?: 'Нет данных' }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Почта</label>
            <p class="text-gray-300">{{ $teacher->email ?: 'Нет данных' }}</p>
         </div>
      </div>

      <div class="flex flex-wrap gap-4 mt-6">
         <form action="{{ route('admin.editTeacher', ['teacher' => $teacher->id]) }}" method="get">
            <button type="submit" class="min-w-48 w-full md:w-1/3 bg-blue-600 hover:bg-blue-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-edit"></i> Редактировать
            </button>
         </form>
         <form action="{{ route('admin.destroyTeacher', ['teacher' => $teacher->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этого преподавателя?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="min-w-48 w-full md:w-1/3 bg-red-500 hover:bg-red-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-trash"></i> Удалить
            </button>
         </form>
      </div>
   @endif
</div>

<script src="{{ asset('js/image-preview.js') }}"></script>
<script src="{{ asset('js/toggle-pass.js') }}"></script>
@include('include.success-message')
@endsection