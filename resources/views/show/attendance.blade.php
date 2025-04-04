@php
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
                <a href="{{ route('CourseScheduleShowLesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" class="text-blue-400 hover:text-blue-300">Расписание</a>
                <span class="text-gray-500">/</span>
            </div>
        </div>

        <div class="min-h-screen bg-gray-900 text-gray-100">
         <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="flex items-center space-x-4 my-4">
                <a href="{{ route('CourseScheduleShowLesson', ['course' => $course->id, 'lesson' => $lesson->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <h1 class="text-3xl font-bold">Посещаемость занятия: {{$lesson->title}} <span class="text-gray-400"></span></h1>
            </div>
             
             <form action="{{ route('CourseScheduleUpdateAttendance', ['course' => $course->id, 'lesson' => $lesson->id]) }}" method="POST">
                 @csrf
                 <input type="hidden" name="group_id" value="{{ $lesson->group_id }}">
                 
                 <div class="bg-gray-800 rounded-lg p-6 shadow">
                     <div class="grid grid-cols-4 gap-4 mb-4 font-medium border-b pb-2">
                         <div>Студент</div>
                         <div>Присутствие</div>
                         <div>Комментарий</div>
                         <div>Статус</div>
                     </div>
                     
                     @foreach($lesson->group->students as $student)
                        <input type="hidden" name="attendances[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                        @php
                           $attendance = $lesson->attendances->firstWhere('student_id', $student->id);
                        @endphp
                        <div class="grid grid-cols-4 gap-4 items-center py-3 border-b border-gray-700">
                           <div>{{ $student->surname }} {{ $student->name }} {{ $student->lastname }}</div>
                           
                           <div>
                              <select name="attendances[{{ $student->id }}][status]" 
                                       class="bg-gray-700 rounded px-3 py-2 w-full">
                                    <option value="present" {{ ($attendance->status ?? 'present') === 'present' ? 'selected' : '' }}>
                                       Присутствовал
                                    </option>
                                    <option value="absent" {{ ($attendance->status ?? null) === 'absent' ? 'selected' : '' }}>
                                       Отсутствовал
                                    </option>
                                    <option value="late" {{ ($attendance->status ?? null) === 'late' ? 'selected' : '' }}>
                                       Опоздал
                                    </option>
                              </select>
                           </div>
                           
                           <div>
                              <input type="text" 
                                       name="attendances[{{ $student->id }}][comment]"
                                       value="{{ $attendance->comment ?? '' }}"
                                       class="bg-gray-700 rounded px-3 py-2 w-full">
                           </div>
                           
                           <div>
                              <input type="hidden" name="attendances[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                              {{ $attendance ? $attendance->statusName() : 'Еще не отмечен' }}
                           </div>
                        </div>
                        @endforeach
                     
                     <div class="mt-6 flex justify-end">
                         <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 rounded-md">
                             Сохранить посещаемость
                         </button>
                     </div>
                 </div>
             </form>
         </div>
     </div>

    </div>
</div>
@endif
@include('include.success-message')
@include('include.error-message') 
@endsection