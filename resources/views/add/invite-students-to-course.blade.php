@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
Пригласить студентов - {{$course->name}}
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
                <a href="{{ route('CourseShow', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">{{ $course->name }}</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseStudents', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Студенты</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">Пригласить студентов</span>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('CourseStudents', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <h1 class="text-3xl font-bold">Приглашение студентов</h1>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <form action="{{ route('teacherCourseInvite', ['course' => $course->id]) }}" method="post">
                @csrf
                
                <div class="space-y-6">
                    <!-- Выбор группы -->
                    <div>
                        <label for="groupSelect" class="block text-lg font-medium mb-3">
                            <i class="fas fa-users mr-2 text-blue-400"></i>
                            Доступные группы
                        </label>
                        <select name="groupId" id="groupSelect" 
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg py-3 px-4 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Выберите группу</option>
                            @forelse ($groups as $group)
                                @if($course->educationPrograms->contains('id', $group->education_program_id))
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endif
                            @empty
                                <option disabled>Нет доступных групп</option>
                            @endforelse
                        </select>
                    </div>

                    <!-- Список студентов -->
                    <div>
                        <div id="studentsList" class="bg-gray-700 p-4 rounded-lg min-h-40">
                            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                <i class="fas fa-users-slash text-4xl mb-3"></i>
                                <p>Выберите группу, чтобы увидеть студентов</p>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка отправки -->
                    <div class="flex justify-center">
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 rounded-lg transition-all duration-200 shadow-md flex items-center space-x-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Отправить приглашения</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#groupSelect').change(function () {
        let groupId = $(this).val();
        let courseId = {{ $course->id }};
        
        if (groupId) {
            $('#studentsList').html(`
                <div class="flex justify-center items-center h-40">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
            `);
            
            $.ajax({
                url: "/course/" + courseId + "/get-group-students/" + groupId,
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (html) {
                    $('#studentsList').html(`
                        <div class="bg-gray-700 rounded-lg overflow-hidden">
                            <div class="p-4 border-b border-gray-600">
                                <h4 class="font-semibold">Студенты группы</h4>
                            </div>
                            <div class="p-4">
                                ${html}
                            </div>
                        </div>
                    `);
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    $('#studentsList').html(`
                        <div class="bg-red-900/50 text-red-300 p-4 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Ошибка загрузки студентов
                        </div>
                    `);
                }
            });
        } else {
            $('#studentsList').html(`
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                    <i class="fas fa-users-slash text-4xl mb-3"></i>
                    <p>Выберите группу, чтобы увидеть студентов</p>
                </div>
            `);
        }
    });
});
</script>

@include('include.success-message')
@endsection