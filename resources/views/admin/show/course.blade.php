@extends('layout.layout')
@section('title') 
Подробнее о курсе
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{ route('admin.showCourses') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Курс "{{ $course->name }}"
      </h1>
   </div>

   @if($editing ?? false)
      <form action="{{ route('admin.updateCourse', $course->id) }}" method="POST" class="space-y-6">
         @csrf
         @method('PUT')
            @if ($errors->any())
               <div class="bg-red-500 text-white p-4 rounded-lg mt-6 shadow-lg">
                  <ul class="space-y-1">
                     @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
            @endif
         <div class="flex flex-col">
            <label for="name" class="text-lg font-medium">Название</label>
            <input type="text" id="name" name="name" value="{{ $course->name }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>
         
         <div class="flex flex-col">
            <label for="description" class="text-lg font-medium">Описание</label>
            <textarea id="description" name="description" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $course->description }}</textarea>
         </div>

         <div class="flex flex-col">
            <label for="teacher" class="text-lg font-medium">Преподаватель курса</label>
            <select id="teacher" name="teacher"
                class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" 
                        @if(isset($course->teachers) && $course->teachers->contains('id', $teacher->id)) selected @endif>
                        {{ $teacher->surname }} {{ $teacher->name }} {{ $teacher->lastname }}
                    </option>
                @endforeach
            </select>
        </div>
         
         <div class="flex flex-col">
            <label for="code" class="text-lg font-medium">Код курса</label>
            <input type="text" id="code" name="code" value="{{ $course->code }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>
         
         <div class="flex flex-col">
            <label for="credits" class="text-lg font-medium">Количество кредитов</label>
            <input type="number" id="credits" name="credits" min="0" step="1" value="{{ $course->credits }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>
         
         <div class="flex flex-col">
            <label for="semester" class="text-lg font-medium">Семестр</label>
            <input type="number" id="semester" name="semester" min="0" max="10" step="1" value="{{ $course->semester }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>
         
         <div class="flex flex-col">
            <label for="degree" class="text-lg font-medium">Степень</label>
            <select id="degree" name="degree" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <option value="Бакалавриат" {{ $course->degree == 'Бакалавриат' ? 'selected' : '' }}>Бакалавриат</option>
               <option value="Магистратура" {{ $course->degree == 'Магистратура' ? 'selected' : '' }}>Магистратура</option>
               <option value="Аспирантура" {{ $course->degree == 'Аспирантура' ? 'selected' : '' }}>Аспирантура</option>
            </select>
         </div>

         <div class="flex flex-col">
            <label for="type" class="text-lg font-medium">Тип</label>
            <select id="type" name="type" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <option value="Обязательный" {{ $course->type == 'Обязательный' ? 'selected' : '' }}>Обязательный</option>
               <option value="Элективный" {{ $course->type == 'Элективный' ? 'selected' : '' }}>Элективный</option>
            </select>
         </div>
         
         <div class="flex flex-wrap gap-4 justify-center md:justify-start">
            <button type="submit" class="w-full md:w-1/3 lg:w-1/6 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-check"></i> Сохранить
            </button>
            <a href="{{ url()->previous() }}" class="w-full md:w-1/3 lg:w-1/6 bg-red-500 hover:bg-red-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-x"></i> Отмена
            </a>
         </div>
      </form>

      @if($course->teachers->isNotEmpty())
      <div class="mt-6">
         <h1 class="text-2xl font-semibold mb-4">Куратор:</h1>
         <ul class="space-y-2">
            @foreach($course->teachers as $teacher)
               <li class="hover:underline flex items-center gap-4">
                  <a href="{{ route('admin.showTeacher', ['teacher' => $teacher->id]) }}" class="text-gray-300 hover:text-gray-400">
                     {{ $teacher->name }} {{ $teacher->surname }} {{ $teacher->lastname }}
                  </a>
                  @if($editing ?? false)
                     <form action="{{ route('admin.detachTeacherCourse', ['course' => $course->id, 'teacher' => $teacher->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите исключить преподавателя {{$teacher->name}} {{$teacher->surname}} {{$teacher->lastname}} из курса {{$course->name}}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">
                           <i class="fas fa-x text-xl text-red-500 hover:text-red-700 transition-all duration-200"></i>
                        </button>
                     </form>
                  @endif
               </li>
            @endforeach
         </ul>
      </div>
   @endif
   @else
      <div class="space-y-4">
         <div class="flex flex-col">
            <label class="text-lg font-medium">Название</label>
            <p class="text-gray-300">{{ $course->name }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Куратор</label>
            <p class="text-gray-300">
                {!! $course->teachers->isNotEmpty() 
                    ? $course->teachers->map(fn($teacher) => 
                        '<a href="'.route('admin.showTeacher', ['teacher' => $teacher->id]).'" class="text-gray-300 hover:underline hover:text-gray-400">'
                        ."{$teacher->surname} {$teacher->name} {$teacher->lastname}</a>"
                    )->join(', ') 
                    : 'Нет данных' 
                !!}
            </p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Описание</label>
            <p class="text-gray-300">{{ $course->description }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Код курса</label>
            <p class="text-gray-300">{{ $course->code }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Количество кредитов</label>
            <p class="text-gray-300">{{ $course->credits }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Семестр</label>
            <p class="text-gray-300">{{ $course->semester }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Тип</label>
            <p class="text-gray-300">{{ $course->type }}</p>
         </div>
      </div>
      <div class="flex flex-wrap gap-4 mt-6">
         <form action="{{ route('admin.editCourse', ['course' => $course->id]) }}" method="get">
            <button type="submit" class="min-w-48 w-full md:w-1/3 bg-blue-600 hover:bg-blue-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-edit"></i> Редактировать
            </button>
         </form>
         <form action="{{ route('admin.destroyCourse', ['course' => $course->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этот курс?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="min-w-48 w-full md:w-1/3 bg-red-500 hover:bg-red-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-trash"></i> Удалить
            </button>
        </form>
      </div>
   @endif

   
</div>

<script src="{{ asset('js/alert-pop-up.js') }}"></script>
@include('include.success-message')
@endsection