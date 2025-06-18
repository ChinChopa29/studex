@extends('layout.layout')
@section('title') 
Выполнение заданий студента {{$student->surname}} {{$student->name}}
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
                <a href="{{ route('CourseShow', $course->id) }}" class="text-blue-400 hover:text-blue-300">{{$course->name}}</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseAssignment', $course->id) }}" class="text-blue-400 hover:text-blue-300">Отчет по выполнению заданий</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">{{$student->surname}} {{$student->name}} {{$student->lastname}}</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseAssignment', ['course' => $course->id, 'group_id' => request('group_id')]) }}" 
                       class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">Выполнение заданий студента <span class="text-blue-400">{{$student->surname}} {{$student->name}} {{$student->lastname}}</span></h1>
                </div>
            </div>
        </div>

        <!-- Основная информация о студенте -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="flex-shrink-0 h-16 w-16 rounded-full bg-gray-700 flex items-center justify-center mr-4">
                        <i class="fas fa-user text-2xl text-gray-400"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">{{$student->surname}} {{$student->name}} {{$student->lastname}}</h2>
                        <p class="text-gray-400">{{$student->email}}</p>
                        <p class="text-gray-400">Группа: {{$group->name}}</p>
                    </div>
                </div>
                <div class="bg-gray-700 rounded-lg p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-sm text-gray-400">Всего заданий</p>
                            <p class="text-xl font-bold">{{$totalTasks}}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Сдано</p>
                            <p class="text-xl font-bold text-blue-400">{{$submittedTasks}}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Проверено</p>
                            <p class="text-xl font-bold text-green-400">{{$reviewedTasks}}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Процент</p>
                            <p class="text-xl font-bold">
                                <span class="{{ $progressPercentage >= 90 ? 'text-green-400' : ($progressPercentage >= 75 ? 'text-blue-400' : ($progressPercentage >= 50 ? 'text-yellow-400' : 'text-red-400')) }}">
                                    {{$progressPercentage}}%
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Таблица выполнения заданий -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-6 flex items-center">
                <i class="fas fa-tasks text-green-400 mr-2"></i>
                Детализация выполнения заданий
            </h2>

            <!-- Фильтры -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <input type="text" id="filter-milestone" placeholder="Фильтр по этапу" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                <input type="text" id="filter-task" placeholder="Фильтр по заданию" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                <select id="filter-status" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                    <option value="">Все статусы</option>
                    <option value="not-submitted">Не сдано</option>
                    <option value="submitted">Сдано</option>
                    <option value="reviewed">Проверено</option>
                </select>
                <input type="text" id="filter-deadline" placeholder="Фильтр по сроку" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Этап</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Задание</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Срок сдачи</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Статус</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Оценка</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Действия</th>
                        </tr>   
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @foreach($milestones as $milestone)
                            @foreach($milestone->tasks as $task)
                                @php
                                    $grade = $student->grades()->where('task_id', $task->id)->first();
                                    $file = \App\Models\StudentTaskFile::where('student_id', $student->id)
                                        ->where('task_id', $task->id)
                                        ->first();
                                    $comment = \App\Models\TaskComment::where('student_id', $student->id)
                                        ->where('task_id', $task->id)
                                        ->first();
                                    
                                    $status = 'not-submitted';
                                    $statusText = 'Не сдано';
                                    $statusClass = 'bg-red-900 text-red-300';
                                    
                                    if ($grade) {
                                        $status = 'reviewed';
                                        $statusText = 'Проверено';
                                        $statusClass = 'bg-green-900 text-green-300';
                                    } elseif ($file || $comment) {
                                        $status = 'submitted';
                                        $statusText = 'Сдано';
                                        $statusClass = 'bg-blue-900 text-blue-300';
                                    }
                                @endphp
                                <tr class="task-row"
                                data-milestone="{{ $milestone->name }}"
                                data-task="{{ $task->name }}"  
                                data-status="{{ $status }}"
                                data-deadline="{{ $task->deadline->format('d.m.Y') }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $milestone->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $task->name }} 
                                        @if($task->description)
                                            <p class="text-xs text-gray-400 mt-1">{{ Str::limit($task->description, 50) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $task->deadline->format('d.m.Y') }}
                                        @if($task->deadline->isPast() && $status === 'not-submitted')
                                            <span class="text-xs text-red-400 block">Просрочено</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-full {{ $statusClass }} text-xs font-medium">{{ $statusText }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($grade)
                                            <span class="font-bold {{ $grade->grade >= 60 ? 'text-green-400' : ($grade->grade >= 40 ? 'text-yellow-400' : 'text-red-400') }}">
                                                {{ $grade->grade }} / 100
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}" 
                                           class="text-blue-400 hover:text-blue-300 mr-2" title="Просмотр задания">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($file || $comment)
                                            <a href="{{ route('CourseTaskShowStudent', ['course' => $course->id, 'task' => $task->id, 'student' => $student->id]) }}" 
                                               class="text-green-400 hover:text-green-300" title="Проверить работу">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Кнопка экспорта -->
            <div class="mt-6 flex justify-end">
                <a href="{{ route('exportStudentAssignment', ['course' => $course->id, 'student' => $student->id, 'group_id' => request('group_id')]) }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-500 rounded-md flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Экспорт в Excel
                </a>
            </div>
        </div>
    </div>
</div>
@endif
<script src="{{asset('js/search-student-assignment.js')}}"></script>
@endsection