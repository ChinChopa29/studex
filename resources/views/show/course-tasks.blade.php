@extends('layout.layout')
@section('title') 
    Доступные задания
@endsection

@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
   $selectedMilestone = request('milestone', 'all');
@endphp

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Хлебные крошки и заголовок -->
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex') }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseShow', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">{{$course->name}}</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">Задания</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseShow', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">{{ $course->name }} <span class="text-gray-400">({{ $course->code }})</span></h1>
                </div>
               
               @if(Auth::guard('teacher')->check())
               <div class="flex items-center gap-4">
                    <a href="{{ route('teacherCourseCreateTask', ['course' => $course->id]) }}" 
                        class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i> Добавить задание
                    </a>
                </div>
                @endif
           </div>
        </div>

        <!-- Вкладки рубежных контролей -->
        <div class="border-b border-gray-700 mb-6">
            <div class="flex space-x-4 overflow-x-auto pb-2">
                <a href="{{ route('CourseTasks', ['course' => $course->id, 'milestone' => 'all']) }}" 
                   class="milestone-tab whitespace-nowrap px-4 py-3 font-medium {{ $selectedMilestone == 'all' ? 'text-white border-blue-500' : 'text-gray-400 border-transparent' }} border-b-2 hover:border-blue-500 transition-all duration-200">
                    <i class="fas fa-list-ul mr-2"></i> Все задания
                </a>
                
                @foreach($milestones as $milestone)
                    <a href="{{ route('CourseTasks', ['course' => $course->id, 'milestone' => $milestone->id]) }}" 
                       class="milestone-tab whitespace-nowrap px-4 py-3 font-medium {{ $selectedMilestone == $milestone->id ? 'text-white border-blue-500' : 'text-gray-400 border-transparent' }} border-b-2 hover:border-blue-500 transition-all duration-200">
                        <i class="fas fa-flag mr-2"></i> {{ $milestone->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Табы статусов заданий -->
        <div class="border-b border-gray-700 mb-6">
            <div class="flex space-x-4">
                <button class="tab-btn px-4 py-3 font-medium text-gray-400 hover:text-white border-b-2 border-transparent hover:border-blue-500 transition-all duration-200" data-tab="available">
                    <i class="fas fa-check-circle mr-2"></i> Доступные
                </button>
                <button class="tab-btn px-4 py-3 font-medium text-gray-400 hover:text-white border-b-2 border-transparent hover:border-blue-500 transition-all duration-200" data-tab="upcoming">
                    <i class="fas fa-clock mr-2"></i> Предстоящие
                </button>
                <button class="tab-btn px-4 py-3 font-medium text-gray-400 hover:text-white border-b-2 border-transparent hover:border-blue-500 transition-all duration-200" data-tab="completed">
                    <i class="fas fa-times-circle mr-2"></i> Завершенные
                </button>
            </div>
        </div>
    
        <!-- Контент табов -->
        <div class="space-y-6">
            <!-- Доступные задания -->
            <div id="available" class="tab-content">
                @php
                    $availableTasks = $selectedMilestone == 'all' 
                        ? $tasks->where('from', '<=', now())->where('deadline', '>', now())
                        : $tasks->where('milestone_id', $selectedMilestone)
                                ->where('from', '<=', now())
                                ->where('deadline', '>', now());
                    
                    $availableTests = $selectedMilestone == 'all' 
                        ? $testTasks->where('from', '<=', now())->where('deadline', '>', now())
                        : $testTasks->where('milestone_id', $selectedMilestone)
                                ->where('from', '<=', now())
                                ->where('deadline', '>', now());
                    
                    $availableItems = $availableTasks->merge($availableTests)->sortBy('deadline');
                @endphp
                
                @if ($availableItems->isNotEmpty())
                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($availableItems as $item)
                            @php
                                $isTest = $item instanceof \App\Models\TestTask;
                                $totalTime = \Carbon\Carbon::parse($item->deadline)->diffInSeconds($item->from);
                                $remainingTime = \Carbon\Carbon::parse($item->deadline)->diffInSeconds(now());
                                $progress = 100 - ($remainingTime / $totalTime * 100);
                                $progress = max(0, min($progress, 100));
                                
                                if(Auth::guard('student')->check()) {
                                    if($isTest) {
                                        $submission = $item->testResults->where('student_id', Auth::id())->first();
                                        $grade = $submission ? $submission->score : null;
                                    } else {
                                        $submission = $item->studentFiles->where('student_id', Auth::id())->first();
                                        $commentSubmission = $item->comments->where('student_id', Auth::id())->first();
                                        $grade = $item->grades->where('student_id', Auth::id())->first();
                                    }
                                }
                            @endphp
                            
                            <a href="{{ $isTest ? route('CourseTestTask', ['course' => $course->id, 'testTask' => $item->id]) : route('CourseTask', ['course' => $course->id, 'task' => $item->id]) }}" class="group">
                                <div class="bg-gray-800 p-5 rounded-xl hover:bg-gray-700 transition-colors duration-200 shadow-lg border-l-4 {{ $isTest ? 'border-green-500' : 'border-blue-500' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center">
                                                <h3 class="text-xl font-semibold group-hover:text-blue-400 transition-colors duration-200">
                                                    {{ $loop->iteration }}. {{ $item->name }}
                                                </h3>
                                                @if($isTest)
                                                    <span class="ml-2 text-xs bg-green-900 text-green-300 px-2 py-0.5 rounded-full">Тест</span>
                                                @endif
                                            </div>
                                            <p class="text-gray-400 mt-1">
                                                Доступно до: {{ \Carbon\Carbon::parse($item->deadline)->translatedFormat('j F Y года') }}
                                            </p>
                                        </div>
                                        <span class="text-sm bg-blue-900 text-blue-300 px-3 py-1 rounded-full">
                                            {{ round($progress) }}%
                                        </span>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="w-full bg-gray-700 h-2 rounded-full">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $progress }}%;"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">Прогресс истечения срока</div>
                                    </div>

                                    @if(Auth::guard('student')->check())
                                    <div class="mt-4 pt-3 border-t border-gray-700">
                                        @if($grade !== null) 
                                            <div class="flex items-center text-green-400">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                <span>Ваша оценка: {{ $isTest ? $grade : optional($grade)->grade }}/100</span>
                                            </div>
                                        @elseif($submission || ($item instanceof \App\Models\Task && $commentSubmission))  
                                            <div class="flex items-center text-yellow-400">
                                                <i class="fas fa-hourglass-half mr-2"></i>
                                                <span>Ожидание проверки</span>
                                            </div>
                                        @else  
                                            <div class="flex items-center text-red-400">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                <span>Вы еще не сдали это {{ $isTest ? 'тест' : 'задание' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-800 p-8 rounded-xl text-center empty-state-message">
                        <i class="fas fa-tasks text-4xl text-gray-600 mb-4"></i>
                        <h3 class="text-xl font-semibold">Нет доступных заданий</h3>
                        <p class="text-gray-400 mt-2">Посмотрите предстоящие или завершенные задания</p>
                    </div>
                @endif
            </div>

            <!-- Предстоящие задания -->
            <div id="upcoming" class="tab-content hidden">
                @php
                    $upcomingTasks = $selectedMilestone == 'all' 
                        ? $tasks->where('from', '>', now())
                        : $tasks->where('milestone_id', $selectedMilestone)
                                ->where('from', '>', now());
                    
                    $upcomingTests = $selectedMilestone == 'all' 
                        ? $testTasks->where('from', '>', now())
                        : $testTasks->where('milestone_id', $selectedMilestone)
                                ->where('from', '>', now());
                    
                    $upcomingItems = $upcomingTasks->merge($upcomingTests)->sortBy('from');
                @endphp
                
                @if ($upcomingItems->isNotEmpty())
                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($upcomingItems as $item)
                            @php
                                $isTest = $item instanceof \App\Models\TestTask;
                            @endphp
                            
                            <a href="{{ $isTest ? route('CourseTestTask', ['course' => $course->id, 'testTask' => $item->id]) : route('CourseTask', ['course' => $course->id, 'task' => $item->id]) }}" class="group">
                                <div class="bg-gray-800 p-5 rounded-xl hover:bg-gray-700 transition-colors duration-200 shadow-lg border-l-4 {{ $isTest ? 'border-green-500' : 'border-blue-500' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center">
                                                <h3 class="text-xl font-semibold group-hover:text-yellow-400 transition-colors duration-200">
                                                    {{ $loop->iteration }}. {{ $item->name }}
                                                </h3>
                                                @if($isTest)
                                                    <span class="ml-2 text-xs bg-green-900 text-green-300 px-2 py-0.5 rounded-full">Тест</span>
                                                @endif
                                            </div>
                                            <p class="text-gray-400 mt-1">
                                                Откроется: {{ \Carbon\Carbon::parse($item->from)->translatedFormat('j F Y года') }}
                                            </p>
                                        </div>
                                        <span class="text-sm bg-yellow-900 text-yellow-300 px-3 py-1 rounded-full">
                                            <i class="fas fa-clock mr-1"></i> Скоро
                                        </span>
                                    </div>

                                    @if(Auth::guard('student')->check())
                                    <div class="mt-4 pt-3 border-t border-gray-700 text-gray-400">
                                        <i class="fas fa-lock mr-2"></i>
                                        <span>{{ $isTest ? 'Тест' : 'Задание' }} еще не доступно</span>
                                    </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-800 p-8 rounded-xl text-center empty-state-message" style="display: none;">
                        <i class="fas fa-calendar-times text-4xl text-gray-600 mb-4"></i>
                        <h3 class="text-xl font-semibold">Нет предстоящих заданий</h3>
                        <p class="text-gray-400 mt-2">Все задания уже доступны или завершены</p>
                    </div>
                @endif
            </div>

            <!-- Завершенные задания -->
            <div id="completed" class="tab-content hidden">
                @php
                    $completedTasks = $selectedMilestone == 'all' 
                        ? $tasks->where('deadline', '<', now())
                        : $tasks->where('milestone_id', $selectedMilestone)
                                ->where('deadline', '<', now());
                    
                    $completedTests = $selectedMilestone == 'all' 
                        ? $testTasks->where('deadline', '<', now())
                        : $testTasks->where('milestone_id', $selectedMilestone)
                                ->where('deadline', '<', now());
                    
                    $completedItems = $completedTasks->merge($completedTests)->sortByDesc('deadline');
                @endphp
                
                @if ($completedItems->isNotEmpty())
                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($completedItems as $item)
                            @php
                                $isTest = $item instanceof \App\Models\TestTask;
                                
                                if(Auth::guard('student')->check()) {
                                    if($isTest) {
                                        $submission = $item->testResults->where('student_id', Auth::id())->first();
                                        $grade = $submission ? $submission->score : null;
                                    } else {
                                        $submission = $item->studentFiles->where('student_id', Auth::id())->first();
                                        $grade = $item->grades->where('student_id', Auth::id())->first();
                                    }
                                }
                            @endphp
                            
                            <a href="{{ $isTest ? route('CourseTestTask', ['course' => $course->id, 'testTask' => $item->id]) : route('CourseTask', ['course' => $course->id, 'task' => $item->id]) }}" class="group">
                                <div class="bg-gray-800 p-5 rounded-xl hover:bg-gray-700 transition-colors duration-200 shadow-lg border-l-4 {{ $isTest ? 'border-green-500' : 'border-blue-500' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center">
                                                <h3 class="text-xl font-semibold group-hover:text-red-400 transition-colors duration-200">
                                                    {{ $loop->iteration }}. {{ $item->name }}
                                                </h3>
                                                @if($isTest)
                                                    <span class="ml-2 text-xs bg-green-900 text-green-300 px-2 py-0.5 rounded-full">Тест</span>
                                                @endif
                                            </div>
                                            <p class="text-gray-400 mt-1">
                                                Завершено: {{ \Carbon\Carbon::parse($item->deadline)->translatedFormat('j F Y года') }}
                                            </p>
                                        </div>
                                        <span class="text-sm bg-red-900 text-red-300 px-3 py-1 rounded-full">
                                            <i class="fas fa-lock mr-1"></i> Завершено
                                        </span>
                                    </div>

                                    @if(Auth::guard('student')->check())
                                    <div class="mt-4 pt-3 border-t border-gray-700">
                                        @if($submission)
                                            @if($grade !== null)
                                                <div class="flex items-center {{ $grade >= 60 ? 'text-green-400' : 'text-red-400' }}">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    <span>Ваша оценка: {{ $isTest ? $grade : optional($grade)->grade }}/100</span>
                                                </div>
                                            @else
                                                <div class="flex items-center text-yellow-400">
                                                    <i class="fas fa-hourglass-half mr-2"></i>
                                                    <span>Ожидание проверки</span>
                                                </div>
                                            @endif
                                        @else
                                            <div class="flex items-center text-red-400">
                                                <i class="fas fa-times-circle mr-2"></i>
                                                <span>Вы не сдали это {{ $isTest ? 'тест' : 'задание' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-800 p-8 rounded-xl text-center empty-state-message" style="display: none;">
                        <i class="fas fa-check-circle text-4xl text-gray-600 mb-4"></i>
                        <h3 class="text-xl font-semibold">Нет завершенных заданий</h3>
                        <p class="text-gray-400 mt-2">Все задания еще активны или предстоят</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
<script src="{{asset('js/tasks-tabs.js')}}"> </script>
@include('include.success-message')
@include('include.error-message') 
@endsection