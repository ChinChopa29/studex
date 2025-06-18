@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
Добавление задания
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
                <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Задания</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">Добавление задания</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseTasks', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">Новое задание</h1>
                </div>
            </div>
        </div>

        <!-- Вкладки для выбора типа задания -->
        <div class="mb-6 border-b border-gray-700">
            <ul class="flex flex-wrap -mb-px" id="taskTypeTabs" data-tabs-toggle="#taskTypeContent">
                <li class="mr-2">
                    <button type="button" 
                            class="inline-block p-4 border-b-2 rounded-t-lg active" 
                            id="regular-task-tab" 
                            data-tabs-target="#regular-task" 
                            aria-current="page">
                        Обычное задание
                    </button>
                </li>
                <li class="mr-2">
                    <button type="button" 
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-300 hover:border-gray-300" 
                            id="test-task-tab" 
                            data-tabs-target="#test-task">
                        Тест
                    </button>
                </li>
            </ul>
        </div>

        <!-- Контент вкладок -->
        <div id="taskTypeContent">
            <!-- Обычное задание -->
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg" id="regular-task" role="tabpanel" aria-labelledby="regular-task-tab">
                <form action="{{route('teacherCourseStoreTask', ['course' => $course->id])}}" method="post" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="task_type" value="regular">
                    
                    <div>
                        <label for="milestone_id" class="block mb-2 font-medium">Выберите рубежный контроль</label>
                        <select name="milestone_id" id="milestone_id" 
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @forelse ($milestones as $milestone)
                                <option value="{{$milestone->id}}" 
                                        data-from="{{$milestone->from}}" 
                                        data-deadline="{{$milestone->deadline}}">
                                    {{$milestone->name}}
                                </option>
                            @empty
                                <option value="null">Вы еще не добавили рубежный контроль</option>
                            @endforelse
                        </select>
                    </div>

                    <div id="milestoneDates" class="mt-2 text-sm text-gray-400">
                        <p>Начало: <span id="milestoneFrom">—</span></p>
                        <p>Дедлайн: <span id="milestoneDeadline">—</span></p>
                    </div>

                    <div>
                        <label for="name" class="block mb-2 font-medium">Название задания</label>
                        <input type="text" id="name" name="name" 
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Введите название задания" value="{{old('name')}}">
                    </div>
                    
                    <div>
                        <label for="description" class="block mb-2 font-medium">Описание</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Добавьте описание задания">{{old('description')}}</textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="from" class="block mb-2 font-medium">Дата начала</label>
                            <input type="date" id="from" name="from" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('from')}}">
                        </div>
                        
                        <div>
                            <label for="deadline" class="block mb-2 font-medium">Дедлайн</label>
                            <input type="date" id="deadline" name="deadline" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('deadline')}}">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Прикрепить файлы</label>
                        <div class="flex items-center space-x-4">
                            <label for="fileInput" class="cursor-pointer bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg border border-gray-600 transition-colors duration-200 flex items-center space-x-2">
                                <i class="fas fa-paperclip"></i>
                                <span>Выберите файлы</span>
                            </label>
                            <input type="file" id="fileInput" name="files[]" multiple class="hidden">
                        </div>
                        <ul id="fileList" class="mt-3 space-y-2"></ul>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" 
                           class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                            Отмена
                        </a>
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Создать задание</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="hidden bg-gray-800 rounded-xl p-6 shadow-lg" id="test-task" role="tabpanel" aria-labelledby="test-task-tab">
                <form action="{{ route('teacherCourseStoreTestTask', ['course' => $course->id]) }}" method="post" class="space-y-6" id="testForm">
                    @csrf
                    <input type="hidden" name="task_type" value="test">
                    
                    <div>
                        <label for="milestone_id_test" class="block mb-2 font-medium">Выберите рубежный контроль</label>
                        <select name="milestone_id" id="milestone_id_test" 
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @forelse ($milestones as $milestone)
                                <option value="{{$milestone->id}}" 
                                        data-from="{{$milestone->from}}" 
                                        data-deadline="{{$milestone->deadline}}">
                                    {{$milestone->name}}
                                </option>
                            @empty
                                <option value="null">Вы еще не добавили рубежный контроль</option>
                            @endforelse
                        </select>
                    </div>
            
                    <div id="milestoneDatesT" class="mt-2 text-sm text-gray-400">
                        <p>Начало: <span id="milestoneFromTest">—</span></p>
                        <p>Дедлайн: <span id="milestoneDeadlineTest">—</span></p>
                    </div>
            
                    <div>
                        <label for="test_name" class="block mb-2 font-medium">Название теста</label>
                        <input type="text" id="test_name" name="name" 
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Введите название теста" value="{{old('name')}}" required>
                    </div>
            
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="test_from" class="block mb-2 font-medium">Дата начала</label>
                            <input type="date" id="test_from" name="from" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('from')}}">
                        </div>
                        
                        <div>
                            <label for="test_deadline" class="block mb-2 font-medium">Дедлайн</label>
                            <input type="date" id="test_deadline" name="deadline" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('deadline')}}">
                        </div>
                    </div>
            
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="shuffle_questions" value="1" 
                                   class="form-checkbox h-5 w-5 text-blue-600 rounded bg-gray-700 border-gray-600">
                            <span class="ml-2 text-gray-300">Перемешивать вопросы</span>
                        </label>
                    </div>
            
                    <!-- Контейнер для вопросов -->
                    <div id="questionsContainer" class="space-y-6">
                        <!-- Вопросы будут добавляться сюда -->
                    </div>
            
                    <!-- Кнопка добавления нового вопроса -->
                    <div class="flex justify-center">
                        <button type="button" id="addQuestionBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Добавить вопрос</span>
                        </button>
                    </div>
            
                    <div class="flex justify-end space-x-4 pt-4">
                        <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" 
                           class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                            Отмена
                        </a>
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Создать тест</span>
                        </button>
                    </div>
                </form>
            </div>

            <template id="questionTemplate">
                <div class="question-item bg-gray-700 p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-medium text-lg">Вопрос <span class="question-number"></span></h3>
                        <button type="button" class="delete-question text-red-400 hover:text-red-300">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-2 font-medium">Текст вопроса</label>
                        <input type="text" name="questions[][text]" 
                               class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Введите текст вопроса" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="questions[][shuffle_answers]" value="1" 
                                   class="question-shuffle form-checkbox h-5 w-5 text-blue-600 rounded bg-gray-600 border-gray-500">
                            <span class="ml-2 text-gray-300">Перемешивать варианты ответа</span>
                        </label>
                    </div>
                    
                    <div class="answers-container space-y-3">
                        <!-- Варианты ответов будут добавляться сюда -->
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="add-answer-btn px-3 py-1 bg-gray-600 hover:bg-gray-500 rounded-lg text-sm flex items-center space-x-1">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Добавить вариант ответа</span>
                        </button>
                    </div>
                </div>
            </template>

            <template id="answerTemplate">
                <div class="answer-item flex items-center space-x-3 bg-gray-600 p-3 rounded">
                    <input type="radio" name="questions[][correct_answer]" value="__INDEX__" class="h-4 w-4 text-blue-600 bg-gray-700 border-gray-600">
                    <input type="text" name="questions[][answers][]" 
                           class="flex-1 px-3 py-1 bg-gray-700 border border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Текст варианта ответа" required>
                    <button type="button" class="delete-answer text-red-400 hover:text-red-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </template>

            
        </div>
    </div>
</div>
@endif
<script src="{{asset('js/add-task.js')}}"></script>
<script src="{{asset('js/add-test.js')}}"></script>
@include('include.success-message')
@include('include.error-message')
@endsection