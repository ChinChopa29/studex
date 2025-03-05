@extends('layout.layout')
@section('title') 
Группы
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{ route('admin.showGroups') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Группа {{ $group->name }}
      </h1>
   </div>

   @if($editing ?? false)
      <form action="{{ route('admin.updateGroup', $group->id) }}" method="POST" class="space-y-6">
         @csrf
         @method('PUT')
         
         <!-- Название группы -->
         <div class="flex flex-col">
            <label for="name" class="text-lg font-medium">Название группы</label>
            <input type="text" id="name" name="name" value="{{ $group->name }}"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>

         <!-- Куратор группы -->
         <div class="flex flex-col">
            <label for="teacher" class="text-lg font-medium">Куратор группы</label>
            <select id="teacher" name="teacher"
                class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" 
                        @if(isset($group->teachers) && $group->teachers->contains('id', $teacher->id)) selected @endif>
                        {{ $teacher->surname }} {{ $teacher->name }} {{ $teacher->lastname }}
                    </option>
                @endforeach
            </select>
        </div>
        

         <!-- Год поступления -->
         <div class="flex flex-col">
            <label for="admission_year" class="text-lg font-medium">Год поступления</label>
            <input type="number" id="admission_year" name="admission_year" value="{{ $group->admission_year }}"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>

         <!-- Год окончания -->
         <div class="flex flex-col">
            <label for="graduation_year" class="text-lg font-medium">Год окончания</label>
            <input type="number" id="graduation_year" name="graduation_year" value="{{ $group->graduation_year }}"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         </div>

         <!-- Образовательная программа -->
         <div class="flex flex-col">
            <label for="education_program_id" class="text-lg font-medium">Образовательная программа</label>
            <select id="education_program_id" name="education_program_id"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               @foreach($educationPrograms as $educationProgram)
                  <option value="{{ $educationProgram->id }}" {{ $group->education_program_id == $educationProgram->id ? 'selected' : '' }}>
                     {{ $educationProgram->title }}
                  </option>
               @endforeach
            </select>
         </div>

         <!-- Кнопки -->
         <div class="flex flex-wrap gap-4 justify-center md:justify-start">
            <button type="submit" class="w-full md:w-1/3 lg:w-1/6 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-check"></i> Сохранить
            </button>
            <a href="{{ url()->previous() }}" class="w-full md:w-1/3 lg:w-1/6 bg-red-500 hover:bg-red-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-x"></i> Отмена
            </a>
        </div>
        
      </form>
   @else
      <div class="space-y-4">
         <div class="flex flex-col">
            <label class="text-lg font-medium">Название группы</label>
            <p class="text-gray-300">{{ $group->name }}</p>
         </div>

         <div class="flex flex-col">
            <label class="text-lg font-medium">Куратор</label>
            <p class="text-gray-300">
                {!! $group->teachers->isNotEmpty() 
                    ? $group->teachers->map(fn($teacher) => 
                        '<a href="'.route('admin.showTeacher', ['teacher' => $teacher->id]).'" class="text-gray-300 hover:underline hover:text-gray-400">'
                        ."{$teacher->surname} {$teacher->name} {$teacher->lastname}</a>"
                    )->join(', ') 
                    : 'Нет данных' 
                !!}
            </p>
        </div>
        

         <div class="flex flex-col">
            <label class="text-lg font-medium">Год поступления</label>
            <p class="text-gray-300">{{ $group->admission_year }}</p>
         </div>

         <div class="flex flex-col">
            <label class="text-lg font-medium">Год окончания</label>
            <p class="text-gray-300">{{ $group->graduation_year }}</p>
         </div>

         <div class="flex flex-col">
            <label class="text-lg font-medium">Образовательная программа</label>
            <p class="text-gray-300">{{ $group->educationProgram->title }}</p>
         </div>
      </div>

      <div class="flex flex-wrap gap-4 mt-6">
         <form action="{{ route('admin.addStudent', ['group' => $group->id]) }}" method="get">
             <button type="submit" class="min-w-64 w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
                 <i class="fas fa-plus"></i> Добавить студентов
             </button>
         </form>
         <form action="{{ route('admin.editGroup', ['group' => $group->id]) }}" method="get">
             <button type="submit" class="min-w-48 w-full md:w-1/3 bg-blue-600 hover:bg-blue-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
                 <i class="fas fa-edit"></i> Редактировать
             </button>
         </form>
         <form action="{{ route('admin.destroyGroup', ['group' => $group->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить эту группу?');">
             @csrf
             @method('DELETE')
             <button type="submit" class="min-w-48 w-full md:w-1/3 bg-red-500 hover:bg-red-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
                 <i class="fas fa-trash"></i> Удалить
             </button>
         </form>
     </div>
     
     
   @endif

   @if($group->teachers->isNotEmpty())
      <div class="mt-6">
         <h1 class="text-2xl font-semibold mb-4">Куратор:</h1>
         <ul class="space-y-2">
            @foreach($group->teachers as $teacher)
               <li class="hover:underline flex items-center gap-4">
                  <a href="{{ route('admin.showTeacher', ['teacher' => $teacher->id]) }}" class="text-gray-300 hover:text-gray-400">
                     {{ $teacher->name }} {{ $teacher->surname }} {{ $teacher->lastname }}
                  </a>
                  @if($editing ?? false)
                     <form action="{{ route('admin.detachTeacher', ['group' => $group->id, 'teacher' => $teacher->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите исключить преподавателя {{$teacher->name}} {{$teacher->surname}} {{$teacher->lastname}} из группы {{$teacher->name}}?');">
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

   @if($group->students->isNotEmpty())
      <div class="mt-6">
         <h1 class="text-2xl font-semibold mb-4">Список студентов:</h1>
         <ul class="space-y-2">
            @foreach($group->students as $student)
               <li class="hover:underline flex items-center gap-4">
                  <a href="{{ route('admin.showUser', ['student' => $student->id]) }}" class="text-gray-300 hover:text-gray-400">
                     {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
                  </a>
                  @if($editing ?? false)
                     <form action="{{ route('admin.detachUser', ['group' => $group->id, 'student' => $student->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите исключить студента {{$student->name}} {{$student->surname}} {{$student->lastname}} из группы {{$group->name}}?');">
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

<script src="{{ asset('js/alert-pop-up.js') }}"></script>

@include('include.success-message')
@endsection