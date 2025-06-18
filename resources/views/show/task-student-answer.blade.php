@php
    $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
    $grade = $task->grades->where('student_id', $student->id)->first();
    $studentFiles = $task->studentFiles->where('student_id', $student->id);
    $studentComments = $task->comments->where('student_id', $student->id);
   
    $allStudents = collect();
    foreach($groups as $group) {
        foreach($group->students as $s) {
            $submission = $task->studentFiles->where('student_id', $s->id)->first();
            $g = $task->grades->where('student_id', $s->id)->first();
            $status = $submission ? ($g ? 'checked' : 'submitted') : 'not_submitted';
            $allStudents->push([
                'id' => $s->id,
                'name' => $s->surname.' '.$s->name,
                'status' => $status
            ]);
        }
    }
    $currentIndex = $allStudents->search(function($item) use ($student) {
        return $item['id'] == $student->id;
    });
    $nextUnchecked = $allStudents->slice($currentIndex + 1)->firstWhere('status', 'submitted');
    $nextNotSubmitted = $allStudents->slice($currentIndex + 1)->firstWhere('status', 'not_submitted');
@endphp

@extends('layout.layout')
@section('title') 
{{$task->name}}
@endsection

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Хлебные крошки и заголовок -->
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseShow', ['course' => $course->id, 'task' => $task->id]) }}" class="text-blue-400 hover:text-blue-300">{{ $course->name }}</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Задания</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}" class="text-blue-400 hover:text-blue-300">{{ $task->name }}</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">{{ $student->surname }} {{ $student->name }}</span>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <h1 class="text-3xl font-bold">Решение студента</h1>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="space-y-6">
            <!-- Информация о задании -->
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">{{ $task->name }}</h2>
                <p class="text-gray-300 mb-6">{{ $task->description }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <div class="flex items-center space-x-2 text-blue-400">
                            <i class="far fa-calendar-alt"></i>
                            <span class="font-medium">Дата начала:</span>
                        </div>
                        <p class="mt-1 text-lg font-semibold">
                            {{ \Carbon\Carbon::parse($task->from)->translatedFormat('j F Y года') }}
                        </p>
                    </div>
                    
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <div class="flex items-center space-x-2 text-red-400">
                            <i class="far fa-clock"></i>
                            <span class="font-medium">Дедлайн:</span>
                        </div>
                        <p class="mt-1 text-lg font-semibold">
                            {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('j F Y года') }}
                        </p>
                    </div>
                </div>
                
                @if($task->teacherFiles->isNotEmpty())
                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-3 flex items-center">
                        <i class="far fa-file-alt mr-2 text-blue-400"></i>
                        Материалы задания
                    </h3>
                    <div class="space-y-2">
                        @foreach($task->teacherFiles as $file)
                        <div class="flex items-center justify-between bg-gray-700 p-3 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                            <div class="flex items-center space-x-3">
                                <i class="far fa-file text-blue-400"></i>
                                <a href="{{ asset('storage/' . $file->file_path) }}" 
                                   class="text-blue-400 hover:underline truncate max-w-xs"
                                   download="{{ $file->original_name }}">
                                    {{ $file->original_name }}
                                </a>
                            </div>
                            <span class="text-xs text-gray-400">{{ round(filesize(public_path('storage/' . $file->file_path)) / 1024) }} KB</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Решение студента -->
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                    <h2 class="text-2xl font-semibold">
                        <i class="fas fa-user-graduate text-yellow-400 mr-2"></i>
                        {{ $student->surname }} {{ $student->name }} {{ $student->lastname }}
                    </h2>
                    
                    @if($grade && $grade->grade !== null)
                    <div class="flex items-center space-x-3 bg-gray-700 px-4 py-2 rounded-lg">
                        <span class="text-lg font-bold {{ $grade->grade >= 60 ? 'text-green-400' : 'text-yellow-400' }}">
                            {{ $grade->grade }}/100
                        </span>
                        @if($grade->grade >= 90)
                        <span class="px-2 py-1 bg-green-900 text-green-300 text-xs rounded-full">Отлично</span>
                        @elseif($grade->grade >= 75)
                        <span class="px-2 py-1 bg-blue-900 text-blue-300 text-xs rounded-full">Хорошо</span>
                        @elseif($grade->grade >= 60)
                        <span class="px-2 py-1 bg-yellow-900 text-yellow-300 text-xs rounded-full">Удовлетворительно</span>
                        @else
                        <span class="px-2 py-1 bg-red-900 text-red-300 text-xs rounded-full">Неудовлетворительно</span>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Файлы студента -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-3 flex items-center">
                        <i class="fas fa-file-upload mr-2 text-blue-400"></i>
                        Прикрепленные файлы
                    </h3>
                    
                    @if($studentFiles->isNotEmpty() || $studentComments->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($studentFiles as $file)
                                <div class="flex items-center justify-between bg-gray-700 p-3 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                    <div class="flex items-center space-x-3">
                                        <i class="far fa-file text-blue-400"></i>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" 
                                        class="text-blue-400 hover:underline truncate max-w-xs"
                                        download="{{ $file->original_name }}">
                                            {{ $file->original_name }}
                                        </a>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ round(filesize(public_path('storage/' . $file->file_path)) / 1024) }} KB</span>
                                </div>
                            @endforeach

                            @if($studentComments->isNotEmpty())
                                <div class="mt-4 p-4 bg-gray-800 rounded-lg">
                                    <h4 class="text-lg font-semibold text-gray-300">Комментарий студента:</h4>
                                    <p class="text-gray-400">{{ $studentComments->last()->comment }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-gray-700 p-4 rounded-lg text-center text-gray-400">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Студент еще не прикрепил решение
                        </div>
                    @endif
                </div>

                <!-- Форма оценки -->
                @if($grade && $grade->grade !== null)
                <div class="bg-gray-700 p-6 rounded-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="space-y-4">
                            <!-- Блок с оценкой и кнопкой -->
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-300">
                                        Текущая оценка: 
                                        <span class="font-bold text-white">{{ $grade->grade }}/100</span>
                                    </h3>
                                </div>
                                
                                <button 
                                    onclick="document.getElementById('regrade-form').classList.toggle('hidden')" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                                >
                                    <i class="fas fa-redo"></i>
                                    <span>Изменить оценку</span>
                                </button>
                            </div>
                        
                            <!-- Блок с комментарием -->
                            @if($grade->comment)
                                <div class="mt-2 p-3 bg-gray-700 rounded-lg">
                                    <p class="text-sm font-medium text-gray-300 mb-1">Комментарий преподавателя:</p>
                                    <p class="text-gray-400">{{ $grade->comment }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <form id="regrade-form" 
                        action="{{route('CourseTaskGradeStudent', ['course' => $course->id, 'task' => $task->id, 'student' => $student->id])}}" 
                        method="post" 
                        class="hidden space-y-4 mt-4">
                        @csrf
                        <input type="hidden" name="regrade" value="regrade">
                        
                        <div>
                            <label for="grade" class="block mb-2 font-medium">Новая оценка</label>
                            <div class="flex items-center space-x-4">
                                <input type="number" 
                                    id="grade"
                                    name="grade" 
                                    min="0" 
                                    max="100" 
                                    value="{{ $grade->grade }}"
                                    class="w-24 px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <label for="comment" class="block mb-2 font-medium">Комментарий</label>
                                <textarea id="comment" name="comment" rows="3" 
                                        class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Введите комментарий">{{old('comment')}}</textarea>
                                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                    <i class="fas fa-save"></i>
                                    <span>Сохранить</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @elseif($task->deadline && \Carbon\Carbon::parse($task->deadline)->lt(now()))
                    <h1 class="font-bold text-xl text-center">Задание закрыто.</h1>
                @else
                <div class="bg-gray-700 p-6 rounded-lg">
                    <h3 class="text-lg font-medium mb-4">Оценить работу</h3>
                    <form action="{{route('CourseTaskGradeStudent', ['course' => $course->id, 'task' => $task->id, 'student' => $student->id])}}" 
                        method="post" 
                        class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="grade" class="block mb-2 font-medium">Оценка (0-100)</label>
                            <div class="flex items-center space-x-4">
                                <input type="number" 
                                    id="grade"
                                    name="grade" 
                                    min="0" 
                                    max="100" 
                                    value="0"
                                    class="w-24 px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">

                                <label for="comment" class="block mb-2 font-medium">Комментарий</label>
                                <textarea id="comment" name="comment" rows="3" 
                                        class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Введите комментарий">{{old('comment')}}</textarea>
                                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                    <i class="fas fa-check"></i>
                                    <span>Подтвердить</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@include('include.success-message')
@include('include.error-message') 
@endsection