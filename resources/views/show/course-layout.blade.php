
@php
    $isTeacher = Auth::guard('teacher')->check();
    $isStudent = Auth::guard('student')->check();
    $rolePrefix = $isTeacher ? 'teacher' : 'student';

    $activeTab = request()->routeIs("CourseShow") ? 'home' :
                 (request()->routeIs("CourseTasks") ? 'tasks' :
                 (request()->routeIs("CourseGrades") ? 'grades' :
                 (request()->routeIs("CourseStudents") ? 'students' :
                 ($isTeacher && request()->routeIs("teacherCourseInviteForm") ? 'invite' : ''))));
@endphp

<div class="border-b border-gray-600 flex space-x-6 pb-2">
   <a href="{{ route("CourseShow", ['course' => $course->id]) }}" 
      class="pb-2 border-b-2 transition 
               {{ $activeTab === 'home' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
      Главная
   </a>
   <a href="{{ route("CourseTasks", ['course' => $course->id]) }}" 
      class="pb-2 border-b-2 transition 
               {{ $activeTab === 'tasks' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
      Задания
   </a>
   <a href="{{ route("CourseGrades", ['course' => $course->id]) }}" 
      class="pb-2 border-b-2 transition 
               {{ $activeTab === 'grades' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
      Оценки
   </a>
   <a href="{{ route("CourseStudents", ['course' => $course->id]) }}" 
      class="pb-2 border-b-2 transition 
               {{ $activeTab === 'students' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
      Студенты
   </a>
   @if($isTeacher)
      <a href="{{ route('teacherCourseInviteForm', ['course' => $course->id]) }}" 
         class="pb-2 border-b-2 transition 
               {{ $activeTab === 'invite' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
         Пригласить студентов
      </a>
   @endif
</div>
