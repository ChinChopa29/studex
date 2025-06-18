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
                    @if($editing ?? false)
                        <h1 class="text-3xl font-bold">Редактирование занятия</h1>
                    @else
                        <h1 class="text-3xl font-bold">Занятие: {{$lesson->title}} <span class="text-gray-400"></span></h1>
                    @endif
                </div>
                @if(auth()->guard('admin')->check() || auth()->guard('teacher')->check())
                <div class="flex space-x-3">
                    @if($editing ?? false)
                        <form action="{{ route('CourseScheduleUpdateLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-500 rounded-md text-sm">
                                <i class="fas fa-save mr-2"></i>Сохранить
                            </button>
                        </form>
                        <a href="{{ route('CourseScheduleShowLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" 
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-500 rounded-md text-sm">
                            <i class="fas fa-times mr-2"></i>Отмена
                        </a>
                    @else
                        <form action="{{ route('CourseScheduleEditLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" method="get">
                            @csrf
                            <button class="px-4 py-2 bg-yellow-600 hover:bg-yellow-500 rounded-md text-sm">
                                <i class="fas fa-edit mr-2"></i>Редактировать</button>
                        </form> 
                        <form action="{{ route('CourseScheduleDeleteLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 hover:bg-red-500 rounded-md text-sm"
                                    onclick="return confirm('Вы уверены, что хотите удалить это занятие?')">
                                <i class="fas fa-trash mr-2"></i>Удалить
                            </button>
                        </form>
                    @endif
                </div>
                @endif
            </div>

            @if($editing ?? false)
                <!-- Форма редактирования -->
                <form action="{{ route('CourseScheduleUpdateLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Левая колонка -->
                        <div class="space-y-6">
                            <!-- Название -->
                            <div>
                                <label for="title" class="block mb-2 font-medium">Название занятия</label>
                                <input type="text" id="title" name="title" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="{{ $lesson->title }}">
                            </div>
                            
                            <!-- Тип занятия -->
                            <div>
                                <label for="type" class="block mb-2 font-medium">Тип занятия</label>
                                <select id="type" name="type" required
                                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="lecture" @if($lesson->type == 'lecture') selected @endif>Лекция</option>
                                    <option value="practice" @if($lesson->type == 'practice') selected @endif>Практика</option>
                                    <option value="lab" @if($lesson->type == 'lab') selected @endif>Лабораторная</option>
                                    <option value="seminar" @if($lesson->type == 'seminar') selected @endif>Семинар</option>
                                    <option value="exam" @if($lesson->type == 'exam') selected @endif>Экзамен</option>
                                </select>
                            </div>
                            
                            <!-- Дата и время -->
                            <div>
                                <label class="block mb-2 font-medium">Дата и время</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="date" name="date" required 
                                        class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                        value="{{ \Carbon\Carbon::parse($lesson->date)->format('Y-m-d') }}">
                                    <input type="time" name="start_time" required
                                        class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                        value="{{ $lesson->start_time }}">
                                    <input type="time" name="end_time" required
                                        class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                        value="{{ $lesson->end_time }}">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Правая колонка -->
                        <div class="space-y-6">
                            <!-- Группа -->
                            <div>
                            <label for="group_id" class="block mb-2 font-medium">Группа</label>
                            <select id="group_id" name="group_id" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" @if($group->id == $lesson->group_id) selected @endif>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            
                            <!-- Аудитория -->
                            <div>
                            <label for="classroom" class="block mb-2 font-medium">Аудитория</label>
                            <input type="text" id="classroom" name="classroom"
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="">
                            </div>
                            
                            <!-- Прикрепленное задание -->
                            <div>
                                <label for="task_id" class="block mb-2 font-medium">Прикрепить задание</label>
                                <select id="task_id" name="task_id"
                                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Не прикреплять задание</option>
                                    @foreach($tasks as $task)
                                        <option value="{{ $task->id }}" @if($task->id == $lesson->task_id) selected @endif>
                                            {{ $task->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Описание -->
                    <div class="mt-6">
                        <label for="description" class="block mb-2 font-medium">Описание</label>
                        <textarea id="description" name="description" rows="3"
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $lesson->description }}</textarea>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="flex justify-end space-x-4 mt-8">
                        <a href="{{ route('CourseScheduleShowLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" 
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                            Отмена
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200">
                            Сохранить изменения
                        </button>
                    </div>
                </form>
            @else
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
                                <span>{{ $lesson->classroom->number }}</span>
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

                    <!-- Прикрепленное задание -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-blue-400 mb-2">Задание: </h3>
                        <div class="py-4">
                            @if($lesson->task_id)
                                <a href="{{route('CourseTask', ['course' => $course->id, 'task' => $lesson->task_id])}}" class="bg-gray-700 p-4 rounded-lg hover:bg-gray-600 transition-all duration-200">Перейти к заданию</a>
                            @else
                                <span class="bg-gray-700 p-4 rounded-lg hover:bg-gray-600 transition-all duration-200">
                                    Задание не прикреплено
                                </span>
                            @endif
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
                @endif
            </div>

            @php
                $editing = $editing ?? false; 
            @endphp

            @if(auth()->guard('admin')->check() || auth()->guard('teacher')->check())
                @if($editing == false)
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
            @endif
        </div>
    </div>
</div>
@endif
@include('include.success-message')
@include('include.error-message') 
@endsection