@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
{{$course->name}}
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
                <span class="text-gray-400">{{$course->name}}</span>
            </div>
            
            <div class="flex items-center justify-between flex-wrap">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CoursesIndex')}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">{{ $course->name }} <span class="text-gray-400">({{ $course->code }})</span></h1>
                </div>
            
                <!-- Кнопка "Расписание" -->
                <a href="{{route('CourseSchedule', ['course' => $course->id])}}" 
                    class="flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors duration-200 mt-4 md:mt-0">
                     <i class="fas fa-calendar mr-2"></i>
                     <span>Расписание</span>
                </a>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Левая колонка - информация о курсе -->
        <div class="lg:col-span-2 mb-6">
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Блок с основной информацией -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                            Основная информация
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-400">Семестр проведения</p>
                                <p class="text-lg font-medium">{{$course->semester}}</p>
                            </div>
                            <div>
                                <p class="text-gray-400">Количество кредитов</p>
                                <p class="text-lg font-medium">{{$course->credits}}</p>
                            </div>
                            <div>
                                <p class="text-gray-400">Тип курса</p>
                                <p class="text-lg font-medium">{{$course->type}}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Блок с преподавателями -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4 flex items-center">
                            <i class="fas fa-chalkboard-teacher text-yellow-400 mr-2"></i>
                            Преподаватели
                        </h2>
                        <div class="space-y-3">
                            @foreach($teachers as $teacher)
                                @if(isset($course->teachers) && $course->teachers->contains('id', $teacher->id))
                                    <a href="{{route('UserProfile', ['type' => 'teacher', 'id' => $teacher->id])}}">
                                        <div class="flex items-center space-x-3 bg-gray-700 p-3 rounded-lg">
                                            <div class="flex-shrink-0">
                                                <img 
                                                src="{{ asset('storage/' . $teacher->image) }}" alt="" class="h-10 w-10 rounded-full object-cover">
                                            </div>
                                            <div>
                                                <p class="font-medium">{{$teacher->surname}} {{$teacher->name}} {{$teacher->lastname}}</p>
                                                <p class="text-sm text-gray-400">{{$teacher->email}}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
        <!-- Блок с описанием -->
        @if($course->description)
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-3 flex items-center">
                <i class="fas fa-align-left text-green-400 mr-2"></i>
                Описание курса
            </h2>
            <div class="prose prose-invert max-w-none bg-gray-700 p-4 rounded-lg">
                {!! $course->description !!}
            </div>
        </div>
        @endif
        <!-- Блок с отчетностью -->
        @if(Auth::guard('teacher')->check())
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-file-alt text-purple-400 mr-2"></i>
                    Отчетность по курсу
                </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{route('CourseAttendance', ['course' => $course->id])}}" class="flex items-center justify-center px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                            <i class="fas fa-user-check text-blue-400 mr-2"></i>
                            <span>Посещаемость</span>
                        </a>
                        <a href="{{route('CourseAssignment', ['course' => $course->id])}}" class="flex items-center justify-center px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                            <i class="fas fa-tasks text-green-400 mr-2"></i>
                            <span>Выполнение заданий</span>
                        </a>
                        <a href="{{route('CoursePerformance', ['course' => $course->id])}}" class="flex items-center justify-center px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                            <i class="fas fa-chart-line text-yellow-400 mr-2"></i>
                            <span>Успеваемость</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
            <!-- Правая колонка - быстрые действия -->
            <div class="space-y-6">
                <!-- Карточка с заданиями -->
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-tasks text-blue-400 mr-2"></i>
                        Задания курса
                    </h2>
                    <p class="text-gray-400 mb-4">Управление и просмотр заданий по курсу</p>
                    <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Перейти к заданиям
                    </a>
                </div>
                <!-- Для преподавателей - дополнительные действия -->
                @if(Auth::guard('teacher')->check())
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-cog text-yellow-400 mr-2"></i>
                        Управление курсом
                    </h2>
                    <div class="space-y-3">
                        <a href="{{ route('teacherCourseCreateTask', ['course' => $course->id]) }}" 
                           class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            <span>Создать задание</span>
                        </a>
                        <a href="{{ route('teacherCourseCreateMilestone', ['course' => $course->id]) }}" 
                            class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i> Управление рубежными контролями    
                        </a>
                        <a href="{{route('CourseStudents', ['course' => $course->id])}}" 
                           class="flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-users mr-2"></i>
                            <span>Управление студентами</span>
                        </a>
                    </div>
                </div>
                @endif

                
                @if(Auth::guard('student')->check())
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-chart-line text-green-400 mr-2"></i>
                        Ваш прогресс
                    </h2>
                
                    @php
                        $student = Auth::user();
                        $milestones = $course->milestones; 
                    @endphp
                
                    <div class="space-y-4">
                        @foreach($milestones as $milestone)
                            @php
                                $regularTasks = $milestone->tasks;
                                $testTasks = $milestone->testTasks;
                
                                $allTasks = $regularTasks->merge($testTasks);

                                $regularGrades = $student->grades->whereIn('task_id', $regularTasks->pluck('id'))->pluck('grade');
                                $testGrades = $student->testResults->whereIn('test_task_id', $testTasks->pluck('id'))->pluck('score');

                                $totalTasks = $allTasks->count();
                                $completedRegularTasks = $regularGrades->count();
                                $completedTestTasks = $testGrades->count();
                                $totalCompleted = $completedRegularTasks + $completedTestTasks;
                
                                $totalGrades = $regularGrades->merge($testGrades);
                                $missingTasksCount = $totalTasks - $totalCompleted;
                                $totalGrades = $totalGrades->merge(array_fill(0, $missingTasksCount, 0));
                
                                $averageGradeForMilestone = $totalGrades->isNotEmpty() ? $totalGrades->avg() : null;
                            @endphp
                
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="font-medium">Прогресс за РК {{ $loop->iteration }} ({{ $milestone->name }})</span>
                                    <span class="font-medium">{{ $totalCompleted }}/{{ $totalTasks }}</span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" 
                                        style="width: {{ $totalTasks > 0 ? ($totalCompleted/$totalTasks)*100 : 0 }}%"></div>
                                </div>
                                <div class="text-xs text-gray-400 mt-1 flex justify-between">
                                    <span>{{ $completedRegularTasks }} обычных заданий</span>
                                    <span>{{ $completedTestTasks }} тестов</span>
                                </div>
                                
                                <div class="flex justify-between mt-2">
                                    <span class="font-medium">Средний балл за РК {{ $loop->iteration }}</span>
                                    <span class="font-medium">{{ $averageGradeForMilestone ? number_format($averageGradeForMilestone, 2) : '—' }}/100</span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-2.5 mt-1">
                                    <div class="bg-blue-600 h-2.5 rounded-full" 
                                        style="width: {{ $averageGradeForMilestone ? $averageGradeForMilestone : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                
                        @php
                            $allMilestoneGrades = [];
                            foreach($milestones as $milestone) {
                                $regularTasks = $milestone->tasks;
                                $testTasks = $milestone->testTasks;
                
                                $allTasks = $regularTasks->merge($testTasks);
                
                                $regularGrades = $student->grades->whereIn('task_id', $regularTasks->pluck('id'))->pluck('grade');
                                $testGrades = $student->testResults->whereIn('test_task_id', $testTasks->pluck('id'))->pluck('score');
                
                                $totalGrades = $regularGrades->merge($testGrades);
                                $missingTasksCount = $allTasks->count() - $totalGrades->count();
                                $totalGrades = $totalGrades->merge(array_fill(0, $missingTasksCount, 0));
                
                                if ($totalGrades->isNotEmpty()) {
                                    $allMilestoneGrades[] = $totalGrades->avg();
                                }
                            }
                            
                            $semesterGrade = !empty($allMilestoneGrades) ? round(array_sum($allMilestoneGrades) / count($allMilestoneGrades), 2) : null;
                        @endphp
                
                        <div class="mt-6">
                            <div class="flex justify-between mb-1">
                                <span class="font-medium">Семестровая оценка</span>
                                <span class="font-medium">{{ $semesterGrade ? $semesterGrade : '—' }}/100</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2.5">
                                <div class="bg-purple-600 h-2.5 rounded-full" 
                                    style="width: {{ $semesterGrade ? $semesterGrade : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection