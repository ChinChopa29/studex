@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')

@section('title') 
Курсы
@endsection

@section('content')
@if($user)
   <div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
      @include('show.course-layout')

      <div class="mt-6 p-4 rounded-xl shadow-inner">
         <h1 class="text-2xl font-bold">{{ $course->name }} ({{ $course->code }})</h1>
         <h2 class="text-xl font-semibold">Список студентов по группам:</h2>

         @if(Auth::guard('teacher')->check())
            @forelse ($groups as $group)
               <div class="mt-4 p-4 bg-slate-700 rounded-xl shadow-md">
                  <h2 class="text-lg font-bold text-white">{{ $group->name }}</h2>

                  @php
                     $studentStatuses = DB::table('student_course')
                        ->where('course_id', $course->id)
                        ->whereIn('student_id', $group->students->pluck('id'))
                        ->pluck('status', 'student_id');

                     $acceptedStudents = collect($group->students)->filter(fn($s) => ($studentStatuses[$s->id] ?? null) === 'accepted');
                     $pendingStudents = collect($group->students)->filter(fn($s) => ($studentStatuses[$s->id] ?? null) === 'pending');
                     $declinedStudents = collect($group->students)->filter(fn($s) => ($studentStatuses[$s->id] ?? null) === 'declined');
                     $notInvitedStudents = collect($group->students)->filter(fn($s) => !isset($studentStatuses[$s->id]));
                  @endphp

                  <div class="grid grid-cols-2 gap-4 mt-2">
                     <div>
                        <h3 class="text-green-400 font-semibold">Приняли приглашение:</h3>
                        @forelse ($acceptedStudents as $student)
                           <a href="{{ route('studentProfile', ['student' => $student->id]) }}" class="block text-white">
                              {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
                           </a>
                        @empty
                           <p class="text-gray-400">Нет принявших</p>
                        @endforelse
                     </div>
                     <div>
                        <h3 class="text-yellow-400 font-semibold">Ожидают подтверждения:</h3>
                        @forelse ($pendingStudents as $student)
                           <a href="{{ route('studentProfile', ['student' => $student->id]) }}" class="block text-white">
                              {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
                           </a>
                        @empty
                           <p class="text-gray-400">Нет ожидающих</p>
                        @endforelse
                     </div>

                     <div>
                        <h3 class="text-red-400 font-semibold">Отклонили приглашение:</h3>
                        @forelse ($declinedStudents as $student)
                           <a href="{{ route('studentProfile', ['student' => $student->id]) }}" class="block text-white">
                              {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
                           </a>
                        @empty
                           <p class="text-gray-400">Нет отклонивших</p>
                        @endforelse
                     </div>

                     <div>
                        <h3 class="text-blue-400 font-semibold">Не приглашены:</h3>
                        @forelse ($notInvitedStudents as $student)
                           <a href="{{ route('studentProfile', ['student' => $student->id]) }}" class="block text-white">
                              {{ $student->name }} {{ $student->surname }} {{ $student->lastname }} 
                           </a>
                        @empty
                           <p class="text-gray-400">Все студенты получили приглашения</p>
                        @endforelse
                     </div>
                  </div>
               </div>
            @empty
               <p class="text-gray-400">Нет групп с записанными студентами</p>
            @endforelse

         @elseif(Auth::guard('student')->check())
            @forelse ($groups as $group)
               <div class="mt-4 p-4 bg-slate-700 rounded-xl shadow-md">
                  <h2 class="text-lg font-bold text-white">{{ $group->name }}</h2>

                  @php
                     $studentStatuses = DB::table('student_course')
                        ->where('course_id', $course->id)
                        ->whereIn('student_id', $group->students->pluck('id'))
                        ->pluck('status', 'student_id');

                     $acceptedStudents = collect($group->students)->filter(fn($s) => ($studentStatuses[$s->id] ?? null) === 'accepted');
                  @endphp

                  <div class="grid grid-cols-1 gap-2 mt-2">
                     @forelse ($acceptedStudents as $student)
                        <a href="{{ route('studentProfile', ['student' => $student->id]) }}" class="block text-white">
                           {{ $student->name }} {{ $student->surname }} {{ $student->lastname }}
                        </a>
                     @empty
                        <p class="text-gray-400">Нет принявших</p>
                     @endforelse
                  </div>
               </div>
            @empty
               <p class="text-gray-400">Нет групп с записанными студентами</p>
            @endforelse
         @endif
      </div>
   </div>
@endif
@endsection

