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
                <div class="flex items-center justify-between space-x-4 bg-gray-800 p-4 rounded-lg">
                <div>
                    <button onclick="changeWeek(-1)" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span id="currentWeekRange" class="font-medium">Загрузка...</span>
                    <button onclick="changeWeek(1)" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button onclick="resetToCurrentWeek()" class="ml-4 px-4 py-2 bg-blue-600 rounded hover:bg-blue-500 text-sm">
                        Текущая неделя
                    </button>
                </div>
                <div>
                    <a href="{{route('CourseScheduleCreateLesson', ['course' => $course->id])}}" class="ml-4 px-4 py-2 bg-blue-600 rounded hover:bg-blue-500 text-sm">
                        Добавить занятие <i class="fas fa-plus mr-2"></i>
                    </a>
                </div>
                </div>
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
                                                                {{ $lesson->classroom }} | {{ $lesson->typeName() }}
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
        let currentDate = new Date();
        let currentWeekStart = new Date();
        
        // Определяем ID курса (безопасная вставка)
        const courseId = {{ json_encode($course->id) }};
        
        function updateLessonsInCalendar(lessons) {
    console.log("Полученные занятия:", lessons);
    
    document.querySelectorAll('tbody td:not(:first-child)').forEach(cell => {
        cell.innerHTML = '<div class="text-gray-500 text-sm">Нет занятий</div>';
    });

    if (!lessons || Object.keys(lessons).length === 0) {
        console.log("Нет занятий для отображения");
        return;
    }

    document.querySelectorAll('tbody tr').forEach(row => {
        const timeCell = row.querySelector('td:first-child');
        const timeText = timeCell.textContent.trim();
        const timeRange = timeText.split(' - ').join('-');

        row.querySelectorAll('td:not(:first-child)').forEach((cell, dayIndex) => {
            const dateHeader = document.querySelector(`th.day-header:nth-child(${dayIndex + 2})`);
            const date = dateHeader.dataset.date;
            
            if (lessons[date] && lessons[date][timeRange]) {
                const lessonsAtTime = lessons[date][timeRange];
                let html = '<div class="space-y-1">';
                
                lessonsAtTime.forEach(lesson => {
                    // Для преподавателей и админов показываем группу
                    const groupInfo = @if(auth()->guard('admin')->check() || auth()->guard('teacher')->check())
                        `<br><span class="text-xs text-gray-300">${lesson.group?.name || ''}</span>`
                    @else
                        ''
                    @endif;
                    
                    html += `
                    <a href="/course/${courseId}/schedule/lessons/${lesson.id}">
                        <div class="lesson-item p-1 mb-1 rounded" style="background-color: ${lesson.color}20">
                            <div class="text-sm font-medium">${lesson.title}</div>
                            <div class="text-xs text-gray-400">
                                ${lesson.classroom} | ${lesson.type}
                                ${groupInfo}
                            </div>
                        </div>
                    </a>`;
                });
                
                html += '</div>';
                cell.innerHTML = html;
            }
        });
    });
}

function updateCalendarDates() {
    const tempDate = new Date(currentWeekStart);
    
    // Обновляем даты в заголовках дней
    document.querySelectorAll('.day-header').forEach((header, index) => {
        const dayDate = new Date(tempDate);
        dayDate.setDate(tempDate.getDate() + index);
        
        const dateStr = dayDate.toISOString().split('T')[0];
        const formattedDate = dayDate.toLocaleDateString('ru-RU', {
            day: 'numeric',
            month: 'numeric'
        });
        
        // Обновляем data-атрибут и отображаемую дату
        header.dataset.date = dateStr;
        const dateElement = header.querySelector('div.text-sm');
        if (dateElement) {
            dateElement.textContent = formattedDate;
        }
    });
    
    // Загружаем занятия для новой недели
    loadLessonsForWeek(currentWeekStart);
}

    function initCalendar() {
        currentDate = new Date();
        currentWeekStart = new Date(currentDate);
        currentWeekStart.setDate(currentDate.getDate() - currentDate.getDay() + (currentDate.getDay() === 0 ? -6 : 1)); // Начало недели (понедельник)
        
        updateWeekDisplay();
        updateCalendarDates();
    }
        
        function updateWeekDisplay() {
            const weekEnd = new Date(currentWeekStart);
            weekEnd.setDate(currentWeekStart.getDate() + 5); 
            
            const options = { day: 'numeric', month: 'numeric', year: 'numeric' };
            const startStr = currentWeekStart.toLocaleDateString('ru-RU', options);
            const endStr = weekEnd.toLocaleDateString('ru-RU', options);
            
            document.getElementById('currentWeekRange').textContent = `${startStr} - ${endStr}`;
        }
        
        function updateCalendarDates() {
    const tempDate = new Date(currentWeekStart);
    
        document.querySelectorAll('.day-header').forEach((header, index) => {
            const dayDate = new Date(tempDate);
            dayDate.setDate(tempDate.getDate() + index);
            
            const dateStr = dayDate.toISOString().split('T')[0];
            const formattedDate = dayDate.toLocaleDateString('ru-RU', {
                day: 'numeric',
                month: 'numeric'
            });
            
            header.dataset.date = dateStr;
            
            const dateElement = header.querySelector('div.text-sm');
            if (dateElement) {
                dateElement.textContent = formattedDate;
            }
        });
        
        loadLessonsForWeek(currentWeekStart);
    }
        
    async function loadLessonsForWeek(startDate) {
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + 5);

    console.log("Загрузка занятий с", startDate.toISOString().split('T')[0], 
                "по", endDate.toISOString().split('T')[0]);

    try {
        const response = await fetch(`/course/${courseId}/schedule/lessons?start=${startDate.toISOString().split('T')[0]}&end=${endDate.toISOString().split('T')[0]}`);
        
        if (!response.ok) {
            const error = await response.json();
            console.error("Ошибка сервера:", error);
            throw new Error(error.error || 'Ошибка загрузки данных');
        }
        
        const lessons = await response.json();
        console.log("Полученные данные:", lessons);
        updateLessonsInCalendar(lessons);
    } catch (error) {
        console.error('Ошибка:', error);
        alert('Ошибка загрузки расписания: ' + error.message);
    }
}


        function changeWeek(weeks) {
            currentWeekStart.setDate(currentWeekStart.getDate() + weeks * 7);
            updateWeekDisplay();
            updateCalendarDates();
        }
        
        function resetToCurrentWeek() {
            currentDate = new Date();
            initCalendar();
        }
        
        document.addEventListener('DOMContentLoaded', initCalendar);
        </script>


        
    @endif
    @endsection