@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user();
@endphp

@extends('layout.layout')
@section('title') 
Добавить занятие - {{$course->name}}
@endsection

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <!-- Хлебные крошки -->
        <div class="flex items-center space-x-2 text-sm mb-6">
            <a href="{{ route('CoursesIndex') }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
            <span class="text-gray-500">/</span>
            <a href="{{ route('CourseShow', $course->id) }}" class="text-blue-400 hover:text-blue-300">{{$course->name}}</a>
            <span class="text-gray-500">/</span>
            <a href="{{ route('CourseSchedule', $course->id) }}" class="text-blue-400 hover:text-blue-300">Расписание</a>
            <span class="text-gray-500">/</span>
            <span class="text-gray-400">Новое занятие</span>
        </div>
        
        <h1 class="text-3xl font-bold mb-6">Добавить занятие</h1>
        
        <!-- Форма -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <form action="{{ route('CourseScheduleStoreLesson', ['course' => $course->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Левая колонка -->
                    <div class="space-y-6">
                        <!-- Название -->
                        <div>
                            <label for="title" class="block mb-2 font-medium">Название занятия</label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Введите название" value="{{ old('name') }}">
                        </div>
                        
                        <!-- Тип занятия -->
                        <div>
                            <label for="type" class="block mb-2 font-medium">Тип занятия</label>
                            <select id="type" name="type" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="lecture" data-color="#3b82f6">Лекция</option>
                                    <option value="practice" data-color="#f59e0b">Практика</option>
                                    <option value="lab" data-color="#10b981">Лабораторная</option>
                                    <option value="seminar" data-color="#8b5cf6">Семинар</option>
                                    <option value="exam" data-color="#ef4444">Экзамен</option>
                            </select>
                        </div>
                        
                        <!-- Дата и время -->
                        <div>
                            <label class="block mb-2 font-medium">Дата и время</label>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="date" name="date" required 
                                       class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('date') }}">
                                <input type="time" name="start_time" required
                                       class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('time') }}">
                                <input type="time" name="end_time" required
                                       class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('end_time') }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Правая колонка -->
                    <div class="space-y-6">
                        <!-- Группа -->
                        <div>
                           <label for="group_id" class="block mb-2 font-medium">Группа</label>
                           <select id="group_id" name="group_id" required
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                               @forelse($groups as $group)
                                   <option value="{{ $group->id }}" @if($loop->first) selected @endif>
                                       {{ $group->name }}
                                   </option>
                               @empty
                                   <option value="" disabled>Нет доступных групп</option>
                               @endforelse
                           </select>
                           @if($course->groups->isEmpty())
                               <p class="text-red-400 text-sm mt-2">Для этого курса не назначены группы</p>
                           @endif
                       </div>
                        
                        <!-- Аудитория -->
                        <div>
                           <label for="classroom" class="block mb-2 font-medium">Аудитория</label>
                           <input type="text" id="classroom" name="classroom"
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Номер аудитории или 'Онлайн'" value="{{ old('classroom') }}">
                        </div>
                        
                        <!-- Повторение -->
                        <div>
                            <label for="recurrence" class="block mb-2 font-medium">Повторение</label>
                            <select id="recurrence" name="recurrence"
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="none">Не повторять</option>
                                <option value="weekly">Каждую неделю</option>
                                <option value="biweekly">Каждые 2 недели</option>
                            </select>
                            <div id="recurrenceEndContainer" class="hidden mt-2">
                                 <label class="block mb-1 text-sm">Повторять до</label>
                                 <select name="milestone_id" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                                    @foreach($milestones as $milestone)
                                       @php
                                          $deadline = is_string($milestone->deadline) 
                                                ? \Carbon\Carbon::parse($milestone->deadline)->format('d.m.Y')
                                                : $milestone->deadline->format('d.m.Y');
                                       @endphp
                                       <option value="{{ $milestone->id }}">
                                          {{ $milestone->name }} (до {{ $deadline }})
                                       </option>
                                    @endforeach
                                 </select>
                            </div>
                        </div>
            
                        <!-- Прикрепленное задание (только для неповторяющихся событий) -->
                        <div id="taskAssignmentContainer" class="hidden">
                            <label for="task_id" class="block mb-2 font-medium">Прикрепить задание</label>
                            <select id="task_id" name="task_id"
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Не прикреплять задание</option>
                                @foreach($course->tasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Описание -->
                <div class="mt-6">
                    <label for="description" class="block mb-2 font-medium">Описание</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Дополнительная информация">{{old('description')}}</textarea>
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('CourseSchedule', $course->id) }}" 
                       class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                        Отмена
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200">
                        Создать занятие
                    </button>
                </div>
            </form>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const recurrenceSelect = document.getElementById('recurrence');
                const taskAssignmentContainer = document.getElementById('taskAssignmentContainer');
                const recurrenceEndContainer = document.getElementById('recurrenceEndContainer');
            
                function toggleContainers() {
                    if (recurrenceSelect.value === 'none') {
                        taskAssignmentContainer.classList.remove('hidden');
                        recurrenceEndContainer.classList.add('hidden');
                    } else {
                        taskAssignmentContainer.classList.add('hidden');
                        recurrenceEndContainer.classList.remove('hidden');
                    }
                }
            
                toggleContainers();
            
                recurrenceSelect.addEventListener('change', toggleContainers);
            });
            </script>
        </div>
    </div>
</div>

<script>
document.getElementById('recurrence').addEventListener('change', function() {
    const container = document.getElementById('recurrenceEndContainer');
    container.style.display = this.value === 'none' ? 'none' : 'block';
});
</script>

<script>
    document.querySelector('form').addEventListener('submit', async function(e) {
    const formData = new FormData(this);
    const response = await fetch('/api/check-time', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    
    if (!result.available) {
        e.preventDefault();
        alert('Это время уже занято в выбранной аудитории');
    }
});
</script>
@endif
@include('include.success-message')
@include('include.error-message')
@endsection