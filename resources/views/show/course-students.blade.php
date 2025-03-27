@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')

@section('title') 
{{ $course->name }} - Студенты
@endsection

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Хлебные крошки и заголовок -->
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex') }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseShow', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">{{ $course->name }}</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">Студенты</span>
            </div>
            
            <div class="flex items-center justify-between">
               <div class="flex items-center space-x-4">
                  <a href="{{ route('CourseShow', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                     <i class="fas fa-arrow-left text-lg"></i>
                  </a>
                  <h1 class="text-3xl font-bold">{{ $course->name }} <span class="text-gray-400">({{ $course->code }})</span></h1>
               </div>
                
                @if(Auth::guard('teacher')->check())
                <a href="{{route('teacherCourseInviteForm', ['course' => $course->id])}}" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                    <i class="fas fa-user-plus mr-2"></i> Добавить студентов
                </a>
                @endif
            </div>
        </div>

        <!-- Основной контент -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-2xl font-semibold mb-6 flex items-center">
                <i class="fas fa-users mr-2 text-yellow-400"></i>
                Список студентов по группам
            </h2>

            @if(!Auth::guard('student')->check())
                @forelse ($groups as $group)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold">{{ $group->name }}</h3>
                        <span class="text-sm bg-gray-700 text-gray-300 px-3 py-1 rounded-full">
                            {{ $group->students->count() }} студентов
                        </span>
                    </div>

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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Принявшие приглашение -->
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-green-400 flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i> Приняли приглашение
                                </h4>
                                <span class="text-xs bg-green-900 text-green-300 px-2 py-1 rounded-full">
                                    {{ $acceptedStudents->count() }}
                                </span>
                            </div>
                            @forelse ($acceptedStudents as $student)
                            <div class="flex items-center justify-between py-2 border-b border-gray-600 last:border-0">
                                <span>{{ $student->surname }} {{ $student->name }} {{ $student->lastname }}</span>
                                <a href="#" class="text-blue-400 hover:text-blue-300 text-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                            @empty
                            <p class="text-gray-400 text-sm">Нет студентов</p>
                            @endforelse
                        </div>

                        <!-- Ожидающие подтверждения -->
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-yellow-400 flex items-center">
                                    <i class="fas fa-hourglass-half mr-2"></i> Ожидают подтверждения
                                </h4>
                                <span class="text-xs bg-yellow-900 text-yellow-300 px-2 py-1 rounded-full">
                                    {{ $pendingStudents->count() }}
                                </span>
                            </div>
                            @forelse ($pendingStudents as $student)
                            <div class="flex items-center justify-between py-2 border-b border-gray-600 last:border-0">
                                <span>{{ $student->surname }} {{ $student->name }} {{ $student->lastname }}</span>
                                <div class="flex space-x-2">
                                    <button class="text-green-400 hover:text-green-300 text-sm">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="text-red-400 hover:text-red-300 text-sm">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-400 text-sm">Нет студентов</p>
                            @endforelse
                        </div>

                        <!-- Отклонившие приглашение -->
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-red-400 flex items-center">
                                    <i class="fas fa-times-circle mr-2"></i> Отклонили приглашение
                                </h4>
                                <span class="text-xs bg-red-900 text-red-300 px-2 py-1 rounded-full">
                                    {{ $declinedStudents->count() }}
                                </span>
                            </div>
                            @forelse ($declinedStudents as $student)
                            <div class="flex items-center justify-between py-2 border-b border-gray-600 last:border-0">
                                <span>{{ $student->surname }} {{ $student->name }} {{ $student->lastname }}</span>
                                <button class="text-blue-400 hover:text-blue-300 text-sm">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                            @empty
                            <p class="text-gray-400 text-sm">Нет студентов</p>
                            @endforelse
                        </div>

                        <!-- Не приглашенные -->
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-blue-400 flex items-center">
                                    <i class="fas fa-envelope mr-2"></i> Не приглашены
                                </h4>
                                <span class="text-xs bg-blue-900 text-blue-300 px-2 py-1 rounded-full">
                                    {{ $notInvitedStudents->count() }}
                                </span>
                            </div>
                            @forelse ($notInvitedStudents as $student)
                            <div class="flex items-center justify-between py-2 border-b border-gray-600 last:border-0">
                                <span>{{ $student->surname }} {{ $student->name }} {{ $student->lastname }}</span>
                                <button class="text-green-400 hover:text-green-300 text-sm">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            @empty
                            <p class="text-gray-400 text-sm">Все студенты приглашены</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-gray-700 p-6 rounded-lg text-center">
                    <i class="fas fa-users-slash text-4xl text-gray-500 mb-4"></i>
                    <h3 class="text-xl font-semibold">Нет групп с записанными студентами</h3>
                    <p class="text-gray-400 mt-2">Добавьте студентов в группы, чтобы они появились здесь</p>
                </div>
                @endforelse

            @elseif(Auth::guard('student')->check())
                @forelse ($groups as $group)
                <div class="mb-6 bg-gray-700 p-5 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">{{ $group->name }}</h3>
                    
                    @php
                        $studentStatuses = DB::table('student_course')
                            ->where('course_id', $course->id)
                            ->whereIn('student_id', $group->students->pluck('id'))
                            ->pluck('status', 'student_id');

                        $acceptedStudents = collect($group->students)->filter(fn($s) => ($studentStatuses[$s->id] ?? null) === 'accepted');
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @forelse ($acceptedStudents as $student)
                        <div class="flex items-center space-x-3 bg-gray-600 p-3 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gray-500 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-300"></i>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">{{ $student->surname }} {{ $student->name }}</p>
                                <p class="text-sm text-gray-400">{{ $student->lastname }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-400">Нет студентов в группе</p>
                        @endforelse
                    </div>
                </div>
                @empty
                <div class="bg-gray-700 p-6 rounded-lg text-center">
                    <i class="fas fa-users-slash text-4xl text-gray-500 mb-4"></i>
                    <h3 class="text-xl font-semibold">Нет групп с записанными студентами</h3>
                </div>
                @endforelse
            @endif
        </div>
    </div>
</div>
@endif
@endsection