@php
    $isTeacher = Auth::guard('teacher')->check();
    $isStudent = Auth::guard('student')->check();

    $activeTab = request()->routeIs(($isTeacher ? 'teacher' : 'student') . 'CourseShow') ? 'home' : 
                 (request()->routeIs(($isTeacher ? 'teacher' : 'student') . 'CourseTasks') ? 'tasks' :
                 (request()->routeIs(($isTeacher ? 'teacher' : 'student') . 'CourseGrades') ? 'grades' :
                 (request()->routeIs(($isTeacher ? 'teacher' : 'student') . 'CourseStudents') ? 'students' : 'teacherCourseInviteForm')));
@endphp

<div class="border-b border-gray-600 flex space-x-6 pb-2">
   <a href="{{ route(($isTeacher ? 'teacher' : 'student') . 'CourseShow', ['course' => $course->id]) }}" 
      class="pb-2 border-b-2 transition 
               {{ $activeTab === 'home' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
      Главная
   </a>
   <a href="{{ route(($isTeacher ? 'teacher' : 'student') . 'CourseTasks', ['course' => $course->id]) }}" 
      class="pb-2 border-b-2 transition 
               {{ $activeTab === 'tasks' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
      Задания
   </a>
   <a href="{{ route(($isTeacher ? 'teacher' : 'student') . 'CourseGrades', ['course' => $course->id]) }}" 
      class="pb-2 border-b-2 transition 
               {{ $activeTab === 'grades' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
      Оценки
   </a>
   <a href="{{ route(($isTeacher ? 'teacher' : 'student') . 'CourseStudents', ['course' => $course->id]) }}" 
      class="pb-2 border-b-2 transition 
               {{ $activeTab === 'students' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
      Студенты
   </a>
   @if($isTeacher)
      <a href="{{ route('teacherCourseInviteForm', ['course' => $course->id]) }}" 
         class="pb-2 border-b-2 transition 
               {{ $activeTab === 'students' ? 'border-white font-semibold' : 'border-transparent hover:border-gray-400' }}">
         Пригласить студентов
      </a>
   @endif
</div>