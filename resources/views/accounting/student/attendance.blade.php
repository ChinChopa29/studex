@extends('layout.layout')
@section('title') 
Посещаемость студента {{$student->surname}} {{$student->name}}
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
                <a href="{{ route('CourseAttendance', $course->id) }}" class="text-blue-400 hover:text-blue-300">Отчет по посещаемости</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">{{$student->surname}} {{$student->name}} {{$student->lastname}}</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseAttendance', ['course' => $course->id, 'group_id' => request('group_id')]) }}" 
                       class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">Посещаемость студента <span class="text-blue-400">{{$student->surname}} {{$student->name}} {{$student->lastname}}</span></h1>
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
                            <p class="text-sm text-gray-400">Всего занятий</p>
                            <p class="text-xl font-bold">{{$totalLessons}}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Присутствовал</p>
                            <p class="text-xl font-bold text-green-400">{{$attendedCount}}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Опозданий</p>
                            <p class="text-xl font-bold text-yellow-400">{{$lateCount}}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Процент</p>
                            <p class="text-xl font-bold">
                                <span class="{{ $attendancePercentage >= 80 ? 'text-green-400' : ($attendancePercentage >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                                    {{$attendancePercentage}}%
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Таблица посещаемости -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-6 flex items-center">
                <i class="fas fa-calendar-alt text-blue-400 mr-2"></i>
                Детализация посещаемости
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <input type="text" id="filter-date" placeholder="Фильтр по дате" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                
                <select id="filter-time" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                    <option value="">Фильтр по времени</option>
                    <option value="08:00">08:00</option>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="12:20">12:20</option>
                    <option value="13:20">13:20</option>
                    <option value="14:20">14:20</option>
                    <option value="15:20">15:20</option>
                    <option value="16:20">16:20</option>
                </select>
                
                <input type="text" id="filter-classroom" placeholder="Фильтр по кабинету" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                <input type="text" id="filter-title" placeholder="Фильтр по теме" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Дата</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Время</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Кабинет</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Тема</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Статус</th>
                        </tr>   
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @foreach($lessons as $lesson)
                        @php
                            $attendance = $attendances[$lesson->id] ?? null;
                            $status = $attendance ? $attendance->status : 'absent';
                        @endphp
                        <tr class="lesson-row"
                            data-date="{{ \Carbon\Carbon::parse($lesson->date)->format('d.m.Y') }}"
                            data-time="{{ \Carbon\Carbon::parse($lesson->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($lesson->end_time)->format('H:i') }}"
                            data-classroom="{{ $lesson->classroom ?? 'Не указан' }}"
                            data-title="{{ $lesson->title ?? 'Тема не указана' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($lesson->date)->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($lesson->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($lesson->end_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $lesson->classroom->number ?? 'Не указан' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $lesson->title ?? 'Тема не указана' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($status === 'present')
                                    <span class="px-2 py-1 rounded-full bg-green-900 text-green-300 text-xs font-medium">Присутствовал</span>
                                @elseif($status === 'late')
                                    <span class="px-2 py-1 rounded-full bg-yellow-900 text-yellow-300 text-xs font-medium">Опоздал</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-red-900 text-red-300 text-xs font-medium">Отсутствовал</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            @if($lessons->hasPages())
            <div class="mt-6">
                {{ $lessons->appends(['group_id' => request('group_id')])->links() }}
            </div>
            @endif

            <!-- Кнопка экспорта -->
            <div class="mt-6 flex justify-end">
                <a href="{{ route('exportStudentAttendance', ['course' => $course->id, 'student' => $student->id, 'group_id' => request('group_id')]) }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-500 rounded-md flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Экспорт в Excel
                </a>
            </div>
        </div>
    </div>
</div>
@endif
<script src="{{asset('js/search-student-attendance.js')}}"></script>
@include('include.success-message')
@include('include.error-message')
@endsection