@extends('layout.layout')
@section('title') 
Профиль студента
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 px-4 py-4 rounded-lg">
   <div class="flex items-center gap-4 mb-8">
      <a href="{{ session('return_to', route('admin.showUsers')) }}"> <i class="fa fa-arrow-left text-2xl hover:text-gray-400 hover:border-gray-400 transition-all duration-200 border-2 rounded-full p-1"></i></a>
      <h1 class="text-2xl"><i class="fas fa-list text-2xl "></i> Студент {{$student->name}} {{$student->surname}}</h1>
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
      <form action="{{ route('admin.updateUser', $student->id) }}" method="POST" class="flex flex-col gap-4">
         @csrf
         @method('PUT')
         
         <h1 class="flex flex-col gap-4">ФИО:
             <input type="text" name="name" value="{{ $student->name }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
             <input type="text" name="surname" value="{{ $student->surname }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
             <input type="text" name="lastname" value="{{ $student->lastname }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
         </h1>
         <h1>ИИН: <input type="text" name="iin" value="{{ $student->iin }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Телефон: <input type="text" name="phone" value="{{ $student->phone }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Пол: 
            <select name="gender" id="gender" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
               <option value="Мужской" {{ $student->gender == 'Мужской' ? 'selected' : '' }}>Мужской</option>
               <option value="Женский" {{ $student->gender == 'Женский' ? 'selected' : '' }}>Женский</option>
           </select>
         </h1>
         <h1>Дата рождения: <input type="date" name="birthday" value="{{ $student->birthday }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Год поступления: <input type="text" name="admission_year" value="{{ $student->admission_year }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Год окончания: <input type="text" name="graduation_year" value="{{ $student->graduation_year }}" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3"></h1>
         <h1>Образовательная программа</h1>
         <select id="degreeSelect" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3" name="education_program_id">
            <option value="{{ $student->educationProgram->id }}">{{ $student->educationProgram->title }}</option>
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
      <div class="flex flex-col gap-4">
         <h1>ФИО: {{$student->name}} {{$student->surname}} {{$student->lastname}}</h1>
         <h1>ИИН: {{$student->iin}}</h1>
         <h1>Телефон: {{$student->phone}}</h1>
         <h1>Пол: {{$student->gender}}</h1>
         <h1>Дата рождения: {{$student->birthday}}</h1>
         <h1>Год поступления: {{$student->admission_year}}</h1>
         <h1>Год окончания: {{$student->graduation_year}}</h1>
         <h1>Группа: {{ $student->groups->pluck('name')->join(', ') ?: 'Нет данных'}}</h1>
         <h1>Почта: {{ $student->email ?: 'Нет данных'}}</h1>
         <h1>Образовательная программа: {{ $student->educationProgram->title}}</h1>
      </div>
      <div class="flex items-center gap-4 mt-4">
         <form action="{{ route('admin.editUser', ['student' => $student->id]) }}" method="get">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition cursor-pointer">
               <i class="fa fa-edit text-xl"></i> Редактировать
            </button>
         </form>
         <form action="{{ route('admin.destroyUser', ['student' => $student->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этого студента?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition cursor-pointer">
               <i class="fa fa-trash text-xl"></i> Удалить
            </button>
         </form>
      </div>
   @endif

   
   
</div>


<script src="{{ asset('js/alert-pop-up.js') }}"></script>

@include('include.success-message')
@endsection
