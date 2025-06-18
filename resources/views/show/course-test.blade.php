@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
{{$testTask->name}}
@endsection

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex') }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseShow', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">{{ $course->name }}</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Задания</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">{{ $testTask->name }}</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseTasks', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">{{ $testTask->name }}</h1>
                </div>
                
                @if(Auth::guard('teacher')->check())
                <div class="flex space-x-3">
                    <form action="{{route('CourseTestDelete', ['course' => $course->id, 'testTask' => $testTask->id])}}" 
                          onsubmit="return confirm('Вы уверены, что хотите удалить этот тест?');" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-trash mr-2"></i> Удалить
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Основной контент -->
        <div class="grid grid-cols-1 gap-8">
            <!-- Левая колонка - информация о тесте -->
            <div class="space-y-6">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-green-500">
                    <div class="prose prose-invert max-w-none">
                        <div class="flex items-center mb-4">
                            <span class="text-xs bg-green-900 text-green-300 px-2 py-0.5 rounded-full mr-2">Тест</span>
                            <span class="text-sm text-gray-400">
                                {{ $testTask->questions->count() }} вопросов
                            </span>
                        </div>
                        
                        <p class="text-gray-300 mb-6">{{ $testTask->description }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center space-x-2 text-blue-400">
                                    <i class="far fa-calendar-alt"></i>
                                    <span class="font-medium">Дата начала:</span>
                                </div>
                                <p class="mt-1 text-lg font-semibold">
                                    {{ \Carbon\Carbon::parse($testTask->from)->translatedFormat('j F Y года') }}
                                </p>
                            </div>
                            
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center space-x-2 text-red-400">
                                    <i class="far fa-clock"></i>
                                    <span class="font-medium">Дедлайн:</span>
                                </div>
                                <p class="mt-1 text-lg font-semibold">
                                    {{ \Carbon\Carbon::parse($testTask->deadline)->translatedFormat('j F Y года') }}
                                </p>
                            </div>
                        </div>
                        
                        @if($testTask->shuffle_questions)
                        <div class="bg-gray-700 p-3 rounded-lg mb-4 flex items-center">
                            <i class="fas fa-random text-blue-400 mr-2"></i>
                            <span>Вопросы будут перемешаны для каждого студента</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Блок для студентов -->
                @if(Auth::guard('student')->check())
                    @php
                        $testResult = $testTask->testResults->where('student_id', Auth::id())->first();
                        $canTakeTest = $testTask->from <= now() && $testTask->deadline > now() && !$testResult;
                    @endphp

                    @if($testResult)
                        <!-- Результат теста -->
                        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                            <h3 class="text-xl font-semibold mb-4">Результат теста</h3>
                            
                            <div class="bg-gray-700 p-4 rounded-lg mb-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-lg">Ваша оценка</h4>
                                        <div class="flex items-center gap-4 mt-2">
                                            <span class="text-2xl font-bold {{ $testResult->score >= 60 ? 'text-green-400' : 'text-yellow-400' }}">
                                                {{ $testResult->score }}/100
                                            </span>
                                            @if($testResult->score >= 90)
                                            <span class="px-2 py-1 bg-green-900 text-green-300 text-xs rounded-full">Отлично</span>
                                            @elseif($testResult->score >= 75)
                                            <span class="px-2 py-1 bg-blue-900 text-blue-300 text-xs rounded-full">Хорошо</span>
                                            @elseif($testResult->score >= 60)
                                            <span class="px-2 py-1 bg-yellow-900 text-yellow-300 text-xs rounded-full">Удовлетворительно</span>
                                            @else
                                            <span class="px-2 py-1 bg-red-900 text-red-300 text-xs rounded-full">Неудовлетворительно</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-400">Верных ответов</p>
                                        <p class="text-lg font-semibold">
                                            {{ $testResult->correct_answers }} из {{ $testResult->total_questions }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($testTask->deadline > now())
                            <div class="bg-gray-700 p-4 rounded-lg flex items-center text-yellow-400">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>Тест можно пройти только один раз</span>
                            </div>
                            @endif
                        </div>
                    @elseif($canTakeTest)
                        <!-- Форма прохождения теста -->
                        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                            <h3 class="text-xl font-semibold mb-4">Прохождение теста</h3>
                            
                            <div class="bg-blue-900 bg-opacity-30 p-4 rounded-lg mb-6 border border-blue-700">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-400 mt-1 mr-2"></i>
                                    <div>
                                        <p class="font-medium mb-1">Инструкция:</p>
                                        <ul class="list-disc list-inside text-sm space-y-1 text-gray-300">
                                            <li>Тест состоит из {{ $testTask->questions->count() }} вопросов</li>
                                            <li>На прохождение теста отводится неограниченное время</li>
                                            <li>После отправки ответов их нельзя будет изменить</li>
                                            <li>Тест можно пройти только один раз</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('SubmitTestTask', ['course' => $course->id, 'testTask' => $testTask->id]) }}" method="POST" id="testForm">
                                @csrf
                                
                                <div class="space-y-6">
                                    @foreach($testTask->questions as $question)
                                        <div class="bg-gray-700 p-5 rounded-lg question-block">
                                            <div class="flex items-start mb-4">
                                                <span class="bg-gray-600 text-white text-sm font-semibold mr-3 px-2.5 py-0.5 rounded-full">{{ $loop->iteration }}</span>
                                                <h4 class="text-lg font-medium">{{ $question->text }}</h4>
                                            </div>
                                            
                                            <div class="space-y-3 answers-container">
                                                @foreach($question->answers as $answer)
                                                    <div class="flex items-center">
                                                        <input type="radio" 
                                                               id="answer-{{ $answer->id }}" 
                                                               name="answers[{{ $question->id }}]" 
                                                               value="{{ $answer->id }}" 
                                                               class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500 focus:ring-2">
                                                        <label for="answer-{{ $answer->id }}" class="ml-2 text-gray-300">{{ $answer->text }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-6 flex justify-end">
                                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>Отправить ответы</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @elseif($testTask->from > now())
                        <!-- Тест еще не начался -->
                        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                            <div class="bg-yellow-900 bg-opacity-30 p-4 rounded-lg border border-yellow-700 text-center">
                                <i class="fas fa-clock text-yellow-400 text-4xl mb-3"></i>
                                <h3 class="text-xl font-semibold mb-2">Тест еще не начался</h3>
                                <p class="text-gray-300">
                                    Тест будет доступен с {{ \Carbon\Carbon::parse($testTask->from)->translatedFormat('j F Y года') }}
                                </p>
                            </div>
                        </div>
                    @elseif($testTask->deadline <= now())
                        <!-- Тест завершен -->
                        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                            <div class="bg-red-900 bg-opacity-30 p-4 rounded-lg border border-red-700 text-center">
                                <i class="fas fa-times-circle text-red-400 text-4xl mb-3"></i>
                                <h3 class="text-xl font-semibold mb-2">Тест завершен</h3>
                                <p class="text-gray-300">
                                    Срок сдачи теста истек {{ \Carbon\Carbon::parse($testTask->deadline)->translatedFormat('j F Y года') }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            @if(Auth::guard('teacher')->check())
            <div class="w-full space-y-6 mt-6">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-blue-400"></i>
                        Статистика теста
                    </h3>
                    
                    @php
                        $totalStudents = $groups->flatMap(fn($group) => $group->students)->unique('id')->count();
                        $completedCount = $testTask->testResults->count();
                        $averageScore = $completedCount > 0 ? round($testTask->testResults->avg('score'), 1) : 0;
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center space-x-2 text-blue-400">
                                <i class="fas fa-users"></i>
                                <span class="font-medium">Всего студентов:</span>
                            </div>
                            <p class="mt-1 text-lg font-semibold">{{ $totalStudents }}</p>
                        </div>
                        
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center space-x-2 text-green-400">
                                <i class="fas fa-check-circle"></i>
                                <span class="font-medium">Прошли тест:</span>
                            </div>
                            <p class="mt-1 text-lg font-semibold">{{ $completedCount }}</p>
                        </div>
                        
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center space-x-2 text-yellow-400">
                                <i class="fas fa-star"></i>
                                <span class="font-medium">Средний балл:</span>
                            </div>
                            <p class="mt-1 text-lg font-semibold">{{ $averageScore }}/100</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="font-medium">Прошли тест</span>
                                <span class="font-medium">{{ $completedCount }}/{{ $totalStudents }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $totalStudents > 0 ? ($completedCount/$totalStudents)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                    
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-users mr-2 text-yellow-400"></i>
                        Результаты студентов
                    </h3>
                    
                    <div class="space-y-6">
                        @foreach($groups->filter(fn($group) => empty($group->subgroup)) as $group)
                            <div data-group-container="{{ $group->id }}" class="group-container">
                                <h4 class="font-semibold text-lg mb-3 text-yellow-400">{{ $group->name }}</h4>
                                
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-gray-700 text-left">
                                                <th class="p-3">Студент</th>
                                                <th class="p-3">Статус</th>
                                                <th class="p-3">Оценка</th>
                                                <th class="p-3">Дата прохождения</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-700">
                                            @foreach($group->students as $student)
                                                @php
                                                    $result = $testTask->testResults->where('student_id', $student->id)->first();
                                                    $status = $result ? 'completed' : 'not_completed';
                                                    $statusText = $result ? '✅ Пройден' : '❌ Не пройден';
                                                    $statusColor = $result ? 'text-green-400' : 'text-red-400';
                                                    $score = $result ? $result->score . '/100' : '—';
                                                    $date = $result ? \Carbon\Carbon::parse($result->created_at)->translatedFormat('j F Y H:i') : '—';

                                                    $studentUrl = route('CourseTestShowStudent', [
                                                        'course' => $course->id,
                                                        'testTask' => $testTask->id,
                                                        'student' => $student->id
                                                    ]);
                                                @endphp

                                                <tr class="hover:bg-gray-700 cursor-pointer student-row" 
                                                    onclick="window.location='{{ $studentUrl }}'"
                                                    data-status="{{ $status }}">
                                                    <td class="p-3">{{ $student->surname }} {{ $student->name }}</td>
                                                    <td class="p-3 font-semibold {{ $statusColor }}">{{ $statusText }}</td>
                                                    <td class="p-3">{{ $score }}</td>
                                                    <td class="p-3">{{ $date }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
<script src="{{asset('js/search-student-tasks.js')}}"></script>
@if(Auth::guard('student')->check() && $canTakeTest)
<script src="{{asset('js/test-assignment-tasks.js')}}"></script>
@endif 
<script src="{{asset('js/search-student-tasks.js')}}"></script>
@include('include.success-message')
@include('include.error-message') 
@endsection