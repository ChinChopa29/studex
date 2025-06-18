@extends('layout.layout')
@section('title') 
Отчет по посещаемости - {{$course->name}}
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
                <span class="text-gray-400">Отчет по посещаемости</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseShow', $course->id) }}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">Отчет по посещаемости <span class="text-gray-400">{{ $course->name }}</span></h1>
                </div>
            </div>
        </div>

        <!-- Выбор группы -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="fas fa-users text-blue-400 mr-2"></i>
                Выберите группу
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($groups->filter(fn($group) => empty($group->subgroup)) as $group)
                  <a href="?group_id={{ $group->id }}" 
                     class="px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200 text-center
                           @if(request('group_id') == $group->id) border-2 border-blue-500 @endif">
                     {{ $group->name }}
                  </a>
               @endforeach
            </div>
        </div>

        <!-- Отчет по посещаемости -->
        @if(request('group_id'))
        @php
            $selectedGroup = $groups->firstWhere('id', request('group_id'));
        @endphp
        
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-6 flex items-center">
                <i class="fas fa-chart-bar text-green-400 mr-2"></i>
                Отчет по группе: {{ $selectedGroup->name }}
            </h2>

            <!-- Поиск студентов -->
            <div class="mb-6">
                <input type="text" id="studentSearch" placeholder="Поиск студентов..." 
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Студент</th>
                            @foreach($course->schedules->groupBy('milestone_id') as $milestoneId => $schedules)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">РК{{ $milestoneId }}</th>
                            @endforeach
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Общий процент</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @foreach($selectedGroup->students as $student)
                            @php
                                $totalLessons = $course->schedules()->where('group_id', $selectedGroup->id)->count();
                                $totalAttended = 0;
                            @endphp
                            <tr class="student-row hover:bg-gray-700 cursor-pointer transition" 
                            data-name="{{ strtolower($student->surname.' '.$student->name.' '.$student->lastname) }}" 
                            data-href="{{ route('CourseStudentAttendance', ['course' => $course->id, 'student' => $student->id]) }}?group_id={{ $selectedGroup->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ $student->surname }} {{ $student->name }} {{ $student->lastname }}</div>
                                            <div class="text-sm text-gray-400">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                @foreach($course->schedules->where('group_id', $selectedGroup->id)->groupBy('milestone_id') as $milestoneId => $schedules)
                                    @php
                                        $milestoneLessons = $schedules->filter(function($schedule) use ($milestoneId) {
                                            return $schedule->milestone_id == $milestoneId;
                                        })->count();
                                        $attendedMilestone = $student->attendances()
                                            ->whereHas('lesson', function($q) use ($milestoneId, $selectedGroup) {
                                                $q->where('milestone_id', $milestoneId)
                                                ->where('group_id', $selectedGroup->id);
                                            })
                                            ->where(function($query) {
                                                $query->where('status', 'present')
                                                    ->orWhere('status', 'late'); 
                                            })
                                            ->count();
                                        $percentageMilestone = $milestoneLessons > 0 ? round(($attendedMilestone / $milestoneLessons) * 100) : 0;
                                        $totalAttended += $attendedMilestone;
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $attendedMilestone }} / {{ $milestoneLessons }} ({{ $percentageMilestone }}%)
                                    </td>
                                @endforeach

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $overallPercentage = $totalLessons > 0 ? round(($totalAttended / $totalLessons) * 100) : 0;
                                    @endphp
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-700 rounded-full h-2.5 mr-2">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $overallPercentage }}%"></div>
                                        </div>
                                        <span>{{ $overallPercentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('exportAttendanceReport', ['course' => $course->id, 'group' => request('group_id')]) }}" 
                       class="px-4 py-2 bg-green-600 hover:bg-green-500 rounded-md flex items-center">
                        <i class="fas fa-file-excel mr-2"></i> Экспорт в Excel
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif
<script src="{{asset('js/seacrh-course-student.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.student-row').forEach(row => {
            row.addEventListener('click', () => {
                const url = row.getAttribute('data-href');
                if (url) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
@endsection
