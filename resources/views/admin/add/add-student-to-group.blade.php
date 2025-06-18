@extends('layout.layout')
@section('title') 
Добавление студента в группу
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{ route('admin.showGroup', ['group' => $group->id]) }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Группа {{ $group->name }}
      </h1>
   </div>

   @if($group->students->isNotEmpty())
      <div class="my-4">
         <h1 class="text-2xl font-semibold mb-4">Список студентов:</h1>
         <ul class="space-y-2">
            @foreach($group->students as $student)
               <li class="hover:underline flex items-center gap-4">
                  <a href="{{ route('admin.showUser', ['student' => $student->id]) }}" class="text-gray-300 hover:text-gray-400">
                     {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
                  </a>
                  <form action="{{ route('admin.detachStudent', ['group' => $group->id, 'student' => $student->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите исключить студента {{$student->name}} {{$student->surname}} {{$student->lastname}} из группы {{$group->name}}?');">
                     @csrf
                     @method('DELETE')
                     <button type="submit">
                        <i class="fas fa-x text-xl text-red-500 hover:text-red-700 transition-all duration-200"></i>
                     </button>
                  </form>
               </li>
            @endforeach
         </ul>
      </div>
   @endif

   <div>
      <div class="flex items-center gap-4">
         <h1 class="text-2xl font-semibold mb-4">Список студентов, которых можно добавить:</h1>
         <span class="tooltip" data-tooltip="Показаны студенты, не состоящие в группах, либо если это подгруппа, состоящии в родительской группе">
            <i class="fas fa-info-circle text-xl"></i>
         </span>
      </div>
      <ul class="space-y-2">
      @forelse ($students as $student)
         @if(!empty($group->subgroup))
            @if($student->groups->contains('id', $group->subgroup) && !$group->students->contains('id', $student->id)) 
               <li class="hover:underline flex items-center gap-4">
                  <div class="flex items-center gap-4 ">
                     <a class="hover:underline flex items-center gap-4 " href="{{ route('admin.showUser', ['student' => $student->id]) }}">
                        {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
                     </a>
                     <form action="{{ route('admin.attachStudent', ['group' => $group->id, 'student' => $student->id]) }}" method="post"
                           onsubmit="return confirm('Вы уверены, что хотите добавить студента {{$student->name}} {{$student->surname}} {{$student->lastname}} в подгруппу {{$group->name}}?');">
                        @csrf
                        <button type="submit">
                              <i class="fas fa-plus text-xl text-green-500 hover:text-green-700 transition-all duration-200"></i>
                        </button>
                     </form>
                  </div>
            @endif
         @else
            @if($student->groups->isEmpty())
            <div class="flex items-center gap-4">
                  <a class="hover:underline flex items-center gap-4" href="{{ route('admin.showUser', ['student' => $student->id]) }}">
                     {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
                  </a>
                  <form action="{{ route('admin.attachStudent', ['group' => $group->id, 'student' => $student->id]) }}" method="post"
                     onsubmit="return confirm('Вы уверены, что хотите добавить студента {{$student->name}} {{$student->surname}} {{$student->lastname}} в группу {{$group->name}}?');">
                     @csrf
                     <button type="submit">
                           <i class="fas fa-plus text-xl text-green-500 hover:text-green-700 transition-all duration-200"></i>
                     </button>
                  </form>
            </div>
            @endif
         @endif
      @empty
         <h1>Студентов еще нет..</h1>
      @endforelse
   </div>

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