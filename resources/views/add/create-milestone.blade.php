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
                <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Задания</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">Добавление рубежного контроля</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseTasks', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
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
                    <input type="number" id="milestone_number" name="milestone_number" 
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
    </div>
</div>
@endif

<script>
document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    let selectedFiles = [];

    fileInput.addEventListener('change', function(event) {
        selectedFiles = [...selectedFiles, ...Array.from(event.target.files)];
        updateFileList();
    });

    function updateFileList() {
        fileList.innerHTML = '';
        
        const dataTransfer = new DataTransfer();
        
        selectedFiles.forEach((file, index) => {
            dataTransfer.items.add(file);
            
            const li = document.createElement('li');
            li.classList.add(
                'flex', 'items-center', 'justify-between', 
                'bg-gray-700', 'p-3', 'rounded-lg', 'shadow'
            );

            const fileInfo = document.createElement('div');
            fileInfo.classList.add('flex', 'items-center', 'space-x-3');
            
            const icon = document.createElement('i');
            icon.classList.add('far', 'fa-file', 'text-blue-400');
            
            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            fileName.classList.add('truncate', 'max-w-xs');
            
            fileInfo.appendChild(icon);
            fileInfo.appendChild(fileName);
            
            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '<i class="fas fa-trash text-red-500 hover:text-red-400"></i>';
            removeBtn.onclick = function() {
                selectedFiles.splice(index, 1);
                updateFileList();
            };

            li.appendChild(fileInfo);
            li.appendChild(removeBtn);
            fileList.appendChild(li);
        });

        fileInput.files = dataTransfer.files;
    }
});
</script>

@include('include.success-message')
@include('include.error-message')
@endsection