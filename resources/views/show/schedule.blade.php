    @php
    $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
    @endphp

    @extends('layout.layout')
    @section('title') 
    {{$course->name}} - Расписание
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
                    <a href="{{ route('CourseShow', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">{{$course->name}}</a>
                    <span class="text-gray-500">/</span>
                    <span class="text-gray-400">Расписание</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseShow', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">Расписание для курса: {{$course->name}} <span class="text-gray-400"></span></h1>
                </div>
                
                <!-- Выбор недели -->
                <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0 md:space-x-4 bg-gray-800 p-4 rounded-lg">

                    {{-- Переключение недель --}}
                    <div class="flex items-center gap-2">
                        <button onclick="changeWeek(-1)" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="currentWeekRange" class="font-medium text-white">Загрузка...</span>
                        <button onclick="changeWeek(1)" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button onclick="resetToCurrentWeek()" class="ml-4 px-4 py-2 bg-blue-600 rounded hover:bg-blue-500 text-sm text-white">
                            Текущая неделя
                        </button>
                    </div>

                    @if(Auth::guard('teacher')->check())
                        {{-- Кнопки преподавателя --}}
                        <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                            <a href="{{ route('CourseScheduleCreateLesson', ['course' => $course->id]) }}"
                            class="bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-sm font-medium text-white shadow-md flex items-center justify-center gap-2">
                                <i class="fas fa-plus"></i> Добавить занятие
                            </a>

                            <form action="{{ route('ScheduleGenerate', ['course' => $course->id]) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="number" name="milestone" placeholder="Номер РК"
                                    class="bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-3 text-sm w-32"
                                    required>
                                <button type="submit"
                                        class="bg-purple-600 hover:bg-purple-700 transition-all duration-200 rounded-lg py-2 px-4 text-sm font-medium text-white shadow-md flex items-center gap-2">
                                    <i class="fas fa-cogs"></i> Сгенерировать
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            <!-- Календарь расписания -->
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="w-28 py-2"></th>
                            @foreach(['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'] as $index => $day)
                                <th class="px-4 py-2 border-b border-gray-700 text-left day-header" data-date="{{ $currentWeekDates[$index] }}">
                                    <div class="font-medium">{{ $day }}</div>
                                    <div class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($currentWeekDates[$index])->format('d.m') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    
                    <tbody>
                        @php
                            $timeSlots = [
                                ['08:00', '08:50', ''],
                                ['09:00', '09:50', ''],
                                ['10:00', '10:50', ''],
                                ['11:00', '11:50', ''],
                                ['12:20', '13:10', ''],
                                ['13:20', '14:10', ''],
                                ['14:20', '15:10', ''],
                                ['15:20', '16:10', ''],
                                ['16:20', '17:10', '']
                            ];
                            
                            $days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
                            $currentWeekDates = [];
                            $tempDate = now()->startOfWeek();
                            
                            for ($i = 0; $i < 6; $i++) {
                                $currentWeekDates[$i] = $tempDate->copy()->addDays($i)->format('Y-m-d');
                            }
                        @endphp
                        
                        @foreach($timeSlots as $slot)
                            <tr class="border-b border-gray-700">
                                <td class="py-3 text-center text-gray-400">
                                    {{ $slot[0] }} - {{ $slot[1] }}
                                </td>
                                
                                @foreach(range(0, 5) as $dayIndex)
                                    @php
                                        $currentDate = $currentWeekDates[$dayIndex];
                                        $timeSlotKey = $slot[0] . '-' . $slot[1];
                                        $lessonsAtTime = $lessons[$currentDate][$timeSlotKey] ?? [];
                                    @endphp
                                    <td class="px-4 py-3 h-20 border-l border-gray-700 hover:bg-gray-700/50 cursor-pointer">
                                        @if(count($lessonsAtTime) > 0)
                                            <div class="space-y-1">
                                                
                                                @foreach($lessonsAtTime as $lesson)
                                                    <a href="{{ route('CourseScheduleShowLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}">
                                                        <div class="lesson-item p-1 mb-1 rounded" style="background-color: {{ $lesson->color }}20">
                                                            <div class="text-sm font-medium">{{ $lesson->title }}</div>
                                                            <div class="text-xs text-gray-400">
                                                                {{ $lesson->classroom->number ?? 'Не указано' }} | {{ $lesson->typeName() }}
                                                                @if(auth()->guard('admin')->check() || auth()->guard('teacher')->check())
                                                                    <br><span class="text-xs text-gray-300">{{ $lesson->group->name ?? '' }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-gray-500 text-sm">Нет занятий</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<script>
    window.courseId = @json($course->id);
    window.isTeacherOrAdmin = @json(auth()->guard('admin')->check() || auth()->guard('teacher')->check());
</script>
<script src="{{ asset('js/schedule.js') }}"></script>
@endif
@if (session()->has('success'))
    <div class="bg-green-600 flex gap-4 p-4 mb-4 rounded-2xl justify-between fixed bottom-4 left-4 z-50" id="alert-div">
        <h1 class="text-white text-base">{{ session('success') }}</h1>
    </div>
@endif

@if (session()->has('error'))
    <div class="bg-red-600 flex gap-4 p-4 mb-4 rounded-2xl justify-between fixed bottom-4 left-4 z-50" id="alert-div">
        <h1 class="text-white text-base">{{ session('error') }}</h1>
    </div>
@endif

@if (session()->has('info'))
    <div class="bg-blue-600 flex gap-4 p-4 mb-4 rounded-2xl justify-between fixed bottom-4 left-4 z-50" id="alert-div">
        <h1 class="text-white text-base">{{ session('info') }}</h1>
    </div>
@endif
@endsection