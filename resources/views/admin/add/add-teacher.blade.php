@extends('layout.layout')
@section('title') 
Добавление преподавателя
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{route('admin.index')}}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Добавление преподавателя
      </h1>
   </div>
   
   @if ($errors->any())
      <div class="bg-red-500 text-white p-4 rounded-lg mb-4 shadow-lg">
         <ul class="space-y-1">
            @foreach ($errors->all() as $error)
               <li>• {{ $error }}</li>
            @endforeach
         </ul>
      </div>
   @endif

   <form action="{{ route('admin.storeTeacher') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
      @csrf
      
      <div class="flex flex-col gap-4">
         <label class="text-lg font-medium">ФИО</label>
         <input type="text" name="name" value="{{ old('name') }}" placeholder="Имя" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         <input type="text" name="surname" value="{{ old('surname') }}" placeholder="Фамилия" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
         <input type="text" name="lastname" value="{{ old('lastname') }}" placeholder="Отчество" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div class="flex flex-col">
         <label class="text-lg font-medium">ИИН</label>
         <input type="text" name="iin" value="{{ old('iin') }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div class="flex flex-col">
         <label class="text-lg font-medium">Телефон</label>
         <input type="text" name="phone" value="{{ old('phone') }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div class="flex flex-col">
         <label class="text-lg font-medium">Пол</label>
         <select name="gender" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="Мужской">Мужской</option>
            <option value="Женский">Женский</option>
         </select>
      </div>
      
      <div class="flex flex-col">
         <label class="text-lg font-medium">Дата рождения</label>
         <input type="date" name="birthday" value="{{ old('birthday') }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div class="flex flex-col">
         <label class="text-lg font-medium">Корпоративная почта</label>
         <input type="email" name="email" value="{{ old('email') }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <div class="flex flex-col">
         <label for="password" class="text-lg font-medium">Пароль</label>
         <input type="password" name="password" value="{{ old('password') }}" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      
      <button class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
         <i class="fas fa-plus"></i> Добавить преподавателя
      </button>
   </form>
</div>
@include('include.success-message')
@endsection
