@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
Добавление рубежного контроля
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
                <span class="text-gray-400">Управление рубежными контролями</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseShow', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">Новый рубежный контроль</h1>
                </div>
            </div>
        </div>

        <!-- Форма создания задания -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <form action="{{route('teacherCourseStoreMilestone', ['course' => $course->id])}}" method="post" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="milestone_number" class="block mb-2 font-medium">Номер рубежного контроля</label>
                    <input type="number" min="0" id="milestone_number" name="milestone_number" 
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Введите номер рубежного контроля" value="{{old('name')}}">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="from" class="block mb-2 font-medium">Дата начала</label>
                        <input type="date" id="from" name="from" 
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('from')}}">
                    </div>
                    
                    <div>
                        <label for="deadline" class="block mb-2 font-medium">Дата окончания</label>
                        <input type="date" id="deadline" name="deadline" 
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('deadline')}}">
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" 
                       class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                        Отмена
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Создать рубежный контроль</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-gray-800 rounded-2xl p-6 shadow-2xl mt-6 text-white">
            <h1 class="font-bold text-2xl mb-4">📋 Список рубежных контролей курса</h1>
        
            @forelse ($milestones as $milestone)
                <div class="bg-gray-700 rounded-xl p-4 mb-4 shadow hover:shadow-xl transition-all duration-300" id="milestone-card-{{ $milestone->id }}">
                    <div class="flex items-center justify-between gap-4" id="milestone-view-{{ $milestone->id }}">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $milestone->name }}</h2>
                            <p class="text-sm text-gray-300">
                                📅 Дата начала: {{ $milestone->from->format('d.m.Y') }} &nbsp;&nbsp;—&nbsp;&nbsp;
                                🕓 Дата окончания: {{ $milestone->deadline->format('d.m.Y') }}
                            </p>
                        </div> 
                        <div class="flex items-center gap-4 text-lg">
                            <button onclick="toggleEdit({{ $milestone->id }})" class="text-blue-400 hover:text-blue-600 transition" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('teacherCourseDestroyMilestone', ['course' => $course->id, 'milestone' => $milestone->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Удалить">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
        
                    <form method="POST" 
                        action="{{ route('teacherCourseUpdateMilestone', ['course' => $course, 'milestone' => $milestone]) }}" 
                        class="mt-4 hidden" id="milestone-form-{{ $milestone->id }}">
                        @csrf
                        @method('PUT')
        
                        <div class="flex flex-col md:flex-row gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm mb-1 text-gray-300">Дата начала:</label>
                                <input type="date" name="from" value="{{ $milestone->from->format('Y-m-d') }}" 
                                       class="w-full rounded px-3 py-2 text-black">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm mb-1 text-gray-300">Дата окончания:</label>
                                <input type="date" name="deadline" value="{{ $milestone->deadline->format('Y-m-d') }}" 
                                       class="w-full rounded px-3 py-2 text-black">
                            </div>
                        </div>
        
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="toggleEdit({{ $milestone->id }})"
                                    class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-xl text-sm">
                                Отмена
                            </button>
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm">
                                💾 Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="text-gray-400 text-center py-6">
                    Пока что нет рубежных контролей для этого курса.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endif

<script>
    function toggleEdit(id) {
        const view = document.getElementById(`milestone-view-${id}`);
        const form = document.getElementById(`milestone-form-${id}`);

        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
            view.classList.add('hidden');
        } else {
            form.classList.add('hidden');
            view.classList.remove('hidden');
        }
    }
</script>

@include('include.success-message')
@include('include.error-message')
@endsection