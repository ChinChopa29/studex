@extends('layout.layout')
@section('title') 
Группы
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 px-4 py-4 rounded-lg">
   <div class="flex items-center gap-4 mb-8">
      <a href="{{ route('admin.showGroups') }}">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400 hover:border-gray-400 transition-all duration-200 border-2 rounded-full p-1"></i>
      </a>
      <h1 class="text-2xl"><i class="fas fa-list text-2xl"></i> Группа {{ $group->name }}</h1>
   </div>

   @if($editing ?? false)
      <form action="{{ route('admin.updateGroup', $group->id) }}" method="POST" class="flex flex-col gap-4">
         @csrf
         @method('PUT')
         
         <h1>Группа: <input type="text" name="name" value="{{ $group->name }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Год поступления: <input type="text" name="admission_year" value="{{ $group->admission_year }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Год окончания: <input type="text" name="graduation_year" value="{{ $group->graduation_year }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Образовательная программа</h1>
         <select id="degreeSelect" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3" name="education_program_id">
            <option value="{{ $group->educationProgram->id }}">{{ $group->educationProgram->title }}</option>
            @foreach($educationPrograms as $educationProgram)
                <option value="{{ $educationProgram->id }}">{{ $educationProgram->title }}</option>
            @endforeach
         </select>

         
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
      <h1>Группа: {{ $group->name }}</h1>
      <h1>Год поступления: {{ $group->admission_year }}</h1>
      <h1>Год окончания: {{ $group->graduation_year }}</h1>
      <h1>Программа: {{ $group->educationProgram->title }}</h1>
      
      <div class="flex items-center gap-4 mt-4">
         <form action="{{ route('admin.addStudent', ['group' => $group->id]) }}" method="get">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition cursor-pointer">
               <i class="fa fa-plus text-xl"></i> Добавить студентов
            </button>
         </form>
         <form action="{{ route('admin.editGroup', ['group' => $group->id]) }}" method="get">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition cursor-pointer">
               <i class="fa fa-edit text-xl"></i> Редактировать
            </button>
         </form>
         <form action="{{ route('admin.destroyGroup', ['group' => $group->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить эту группу?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition cursor-pointer">
               <i class="fa fa-trash text-xl"></i> Удалить
            </button>
         </form>
      </div>
   @endif

   @if($group->students->isNotEmpty())
      <div class="mt-4">
         <h1 class="mb-2 text-2xl">Список студентов:</h1>
         <ul>
            @foreach($group->students as $student)
               <li class="hover:underline flex items-center gap-4">
                  <a href="{{ route('admin.showUser', ['student' => $student->id]) }}">
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
   <div class="bg-red-500 text-white p-4 rounded-lg mt-4">
      <ul>
         @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
         @endforeach
      </ul>
   </div>
   @endif
</div>

<script src="{{ asset('js/alert-pop-up.js') }}"></script>
@include('include.success-message')
@endsection