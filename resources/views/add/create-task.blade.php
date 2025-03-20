@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
Задания
@endsection

@section('content')
@if($user)
   <div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
      
      @include('show.course-layout')
      <div class="flex items-center gap-4 my-6">
         <h2 class="text-2xl font-bold text-gray-200">Создать задание</h2>
      </div>

      <form action="{{route('teacherCourseStoreTask', ['course' => $course->id])}}" method="post" enctype="multipart/form-data" class="space-y-6">
         @csrf
         <div>
            <label class="block text-gray-200 mb-1">Название:</label>
            <input type="text" name="name" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-green-500">
         </div>

         <div>
            <label class="block text-gray-200 mb-1">Описание:</label>
            <textarea name="description" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-green-500" rows="4"></textarea>
         </div>

         <div class="flex gap-4">
            <div>
               <label class="block text-gray-200 mb-1">Начинается:</label>
               <input type="date" name="from" class="p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-green-500">
            </div>
            <div>
               <label class="block text-gray-200 mb-1">Заканчивается:</label>
               <input type="date" name="deadline" class="p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-green-500">
            </div>
         </div>

         <div class="flex flex-col gap-2">
            <label class="block text-gray-200">Прикрепить файлы:</label>
            <label for="fileInput" class="cursor-pointer bg-gray-700 text-white py-2 px-4 rounded-lg border border-gray-600 flex items-center gap-2 w-max hover:bg-gray-600 transition">
                <i class="fa fa-paperclip"></i> Выберите файлы
            </label>
            <input type="file" id="fileInput" name="files[]" multiple class="hidden">
         </div>
        
         <ul id="fileList" class="mt-3 space-y-2 text-white"></ul>

         <button type="submit" class="w-full p-3 bg-green-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-green-700 transition">
            Создать <i class="fa fa-plus"></i>
         </button>
      </form>
   </div>
@endif

<script>
   document.getElementById('fileInput').addEventListener('change', function(event) {
       let fileList = document.getElementById('fileList');
       fileList.innerHTML = ''; 
       let files = Array.from(event.target.files);

       files.forEach((file, index) => {
           let li = document.createElement('li');
           li.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-800', 'p-2', 'rounded-lg', 'shadow');

           let fileName = document.createElement('span');
           fileName.textContent = file.name;

           let removeBtn = document.createElement('button');
           removeBtn.innerHTML = '<i class="fa fa-trash text-red-500 hover:text-red-700"></i>';
           removeBtn.classList.add('ml-3');
           removeBtn.onclick = function() {
               files.splice(index, 1); 
               updateFileList(files);
           };

           li.appendChild(fileName);
           li.appendChild(removeBtn);
           fileList.appendChild(li);
       });

       function updateFileList(updatedFiles) {
           fileList.innerHTML = '';
           updatedFiles.forEach((file, index) => {
               let li = document.createElement('li');
               li.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-800', 'p-2', 'rounded-lg', 'shadow');

               let fileName = document.createElement('span');
               fileName.textContent = file.name;

               let removeBtn = document.createElement('button');
               removeBtn.innerHTML = '<i class="fa fa-trash text-red-500 hover:text-red-700"></i>';
               removeBtn.classList.add('ml-3');
               removeBtn.onclick = function() {
                   updatedFiles.splice(index, 1);
                   updateFileList(updatedFiles);
               };

               li.appendChild(fileName);
               li.appendChild(removeBtn);
               fileList.appendChild(li);
           });
       }
   });
</script>
@include('include.success-message')
@include('include.error-message')
@endsection