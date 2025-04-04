@php
    use Carbon\Carbon;  
    $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
{{$lesson->title}}
@endsection

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Хлебные крошки -->
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex') }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseShow', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">{{$course->name}}</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseSchedule', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Расписание</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">{{ $lesson->title }}</span>
            </div>
        </div>

        

        <!-- Основная информация о занятии -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-6">
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseSchedule', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">Занятие: {{$lesson->title}} <span class="text-gray-400"></span></h1>
                </div>
                @if(auth()->guard('admin')->check() || auth()->guard('teacher')->check())
                <div class="flex space-x-3">
                    <a href="{{ route('CourseScheduleEditLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" 
                       class="px-4 py-2 bg-yellow-600 hover:bg-yellow-500 rounded-md text-sm">
                        <i class="fas fa-edit mr-2"></i>Редактировать
                    </a>
                    <form action="{{ route('CourseScheduleDeleteLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-500 rounded-md text-sm"
                                onclick="return confirm('Вы уверены, что хотите удалить это занятие?')">
                            <i class="fas fa-trash mr-2"></i>Удалить
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Левая колонка - основная информация -->
                <div>
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-blue-400 mb-2">Основная информация</h3>
                        <div class="space-y-2">
                            <div class="flex">
                                <span class="text-gray-400 w-32">Тип:</span>
                                <span>{{ $lesson->typeName() }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-400 w-32">Дата:</span>
                                <span>{{ Carbon::parse($lesson->date)->translatedFormat('j F Y') }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-400 w-32">Время:</span>
                                <span>{{ Carbon::parse($lesson->start_time)->format('H:i') }} - {{ Carbon::parse($lesson->end_time)->format('H:i') }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-400 w-32">Аудитория:</span>
                                <span>{{ $lesson->classroom }}</span>
                            </div>
                            @if($lesson->group)
                            <div class="flex">
                                <span class="text-gray-400 w-32">Группа:</span>
                                <span>{{ $lesson->group->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    

                    <!-- Описание занятия -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-blue-400 mb-2">Описание</h3>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            {!! $lesson->description ?? '<span class="text-gray-500">Описание отсутствует</span>' !!}
                        </div>
                    </div>
                </div>

                <!-- Правая колонка - материалы и посещаемость -->
                <div>
                    <!-- Материалы занятия -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-blue-400 mb-2">Материалы</h3>
                        @if($lesson->materials->count() > 0)
                        <div class="space-y-2">
                            @foreach($lesson->materials as $material)
                                <div class="flex items-center justify-between bg-gray-700 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-alt text-gray-400 mr-3"></i>
                                        <div>
                                            <div>{{ $material->name }}</div>
                                            @if($material->description)
                                            <div class="text-xs text-gray-400">{{ $material->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ asset('storage/' . $material->path) }}" 
                                            class="text-blue-400 hover:text-blue-300 px-2"
                                            download>
                                             <i class="fas fa-download"></i>
                                         </a>
                                        @if(auth()->guard('admin')->check() || auth()->guard('teacher')->check())
                                        <form action="{{ route('CourseScheduleDeleteMaterial', [
                                            'course' => $course->id,
                                            'lesson' => $lesson->id,
                                            'material' => $material->id
                                        ]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-400 hover:text-red-300 px-2"
                                                    onclick="return confirm('Удалить этот материал?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                        </div>
                        @else
                        <div class="text-gray-500 bg-gray-700 p-4 rounded-lg">
                            Материалы отсутствуют
                        </div>
                        @endif
                    </div>

                    @if(auth()->guard('admin')->check() || auth()->guard('teacher')->check())
                        <div class="mt-4 p-4 border border-dashed border-gray-600 rounded-lg">
                            <h4 class="font-medium text-blue-400 mb-3">Добавить новый материал</h4>
                            <form action="{{ route('CourseScheduleStoreMaterial', ['course' => $course->id, 'lesson' => $lesson->id]) }}" 
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="col-span-2">
                                        <input type="file" name="file" id="file" 
                                            class="w-full text-sm text-gray-400
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-md file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-600 file:text-white
                                                    hover:file:bg-blue-500">
                                        @error('file')
                                            <span class="text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <input type="text" name="name" placeholder="Название материала" 
                                            class="w-full px-3 py-2 bg-gray-700 rounded text-sm">
                                        @error('name')
                                            <span class="text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <textarea name="description" rows="2" placeholder="Описание (необязательно)"
                                            class="w-full px-3 py-2 bg-gray-700 rounded text-sm"></textarea>
                                </div>
                                <button type="submit" 
                                        class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-md text-sm">
                                    <i class="fas fa-upload mr-2"></i>Загрузить материал
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                
            </div>
            @if(auth()->guard('admin')->check() || auth()->guard('teacher')->check())
                <div class="w-full">
                    <div class="flex justify-between items-center mb-2 mt-4">
                        <h3 class="text-lg font-semibold text-blue-400">Посещаемость</h3>
                        <a href="{{ route('CourseScheduleAttendance', ['course' => $course->id, 'lesson' => $lesson->id]) }}" 
                            class="px-3 py-1 bg-green-600 hover:bg-green-500 rounded-md text-sm">
                            <i class="fas fa-user-check mr-1"></i>Отметить
                        </a>
                    </div>
                    
                    @if($lesson->attendances()->exists())
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="grid grid-cols-4 gap-2 mb-2 font-medium">
                                <span>Студент</span>
                                <span>Группа</span>
                                <span>Статус</span>
                                <span>Комментарий</span>
                            </div>
                            @foreach($lesson->attendances as $attendance)
                            <div class="grid grid-cols-4 gap-2 py-2 border-t border-gray-600">
                                <span>{{ $attendance->student->surname }} {{ $attendance->student->name }} {{ $attendance->student->lastname }}</span>
                                <span>{{ $attendance->group->name }}</span>
                                <span>
                                    <span class="px-2 py-1 rounded text-xs 
                                        {{ $attendance->status === 'present' ? 'bg-green-900 text-green-300' : 
                                            ($attendance->status === 'absent' ? 'bg-red-900 text-red-300' : 'bg-yellow-900 text-yellow-300') }}">
                                        {{ $attendance->statusName() }}
                                    </span>
                                </span>
                                <span class="text-sm text-gray-400">{{ $attendance->comment ?? '-' }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-gray-500 bg-gray-700 p-4 rounded-lg">
                            Посещаемость не отмечена
                        </div>
                        @endif
                    </div>
            @endif
        </div>
    </div>
</div>
@endif
@include('include.success-message')
@include('include.error-message') 
@endsection