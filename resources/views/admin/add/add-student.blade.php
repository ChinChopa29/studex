@extends('layout.layout')
@section('title') 
Добавление студентов 
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{route('admin.index') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Добавление студентов
      </h1>
   </div>

   <a href="{{route('admin.createUserOne')}}" class="w-full md:w-1/3 bg-blue-600 hover:bg-blue-700 transition duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2 mb-4">
      <i class="fa fa-edit text-xl"></i> Добавить одного
   </a>
   
   <form action="{{ route('admin.storeUser') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
      @csrf
         <label for="csvFile" class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2 cursor-pointer">
            <i class="fa fa-file text-xl"></i> Загрузить файл .csv
         </label>
         <input type="file" id="csvFile" name="students" accept=".csv" class="hidden">

         <select id="degreeSelect" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Выберите степень</option>
            @foreach($educationPrograms->pluck('degree')->unique() as $degree)
               <option value="{{ $degree }}">{{ $degree }}</option>
            @endforeach
         </select>
      
         <select id="programSelect" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <option id="defaultProgramOption" value="">Сначала выберите степень</option>
         </select>
      
      <button type="submit" class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2 mb-4">
          <i class="fa fa-plus text-xl"></i> Создать
      </button>
   </form>

   <button id="uploadBtn" class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
      <i class="fa fa-eye text-xl"></i> Предпросмотр
   </button>

   <table id="csvTable" class="min-w-full border-collapse border mt-4 hidden">
      <thead>
         <tr>
            <th class="border border-slate-500 px-4 py-2">Имя</th>
            <th class="border border-slate-500 px-4 py-2">Фамилия</th>
            <th class="border border-slate-500 px-4 py-2">Отчество</th>
            <th class="border border-slate-500 px-4 py-2">ИИН</th>
            <th class="border border-slate-500 px-4 py-2">Телефон</th>
            <th class="border border-slate-500 px-4 py-2">Пол</th>
            <th class="border border-slate-500 px-4 py-2">Дата рождения</th>
            <th class="border border-slate-500 px-4 py-2">Год поступления</th>
            <th class="border border-slate-500 px-4 py-2">Год окончания</th>
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>

<script>
   document.getElementById('uploadBtn').addEventListener('click', function() {
      document.getElementById('csvTable').classList.toggle('hidden'); 
   });

   document.getElementById('csvFile').addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function(e) {
         const text = e.target.result;
         const rows = text.split("\n").map(row => row.split(",")); 

         const tableBody = document.querySelector("#csvTable tbody");
         tableBody.innerHTML = ""; 

         rows.forEach((row, index) => {
            if (index === 0) return; 

            const tr = document.createElement("tr");
            row.forEach(cell => {
               const td = document.createElement("td");
               td.textContent = cell.trim();
               td.classList.add("border", "border-slate-500", "px-4", "py-2");
               tr.appendChild(td);
            });
            tableBody.appendChild(tr);
         });

         document.getElementById('csvTable').classList.remove("hidden"); 
      };

      reader.readAsText(file);
   });

   document.addEventListener("DOMContentLoaded", function () {
       const degreeSelect = document.getElementById("degreeSelect");
       const programSelect = document.getElementById("programSelect");

       if (!degreeSelect || !programSelect) {
           console.error("Один из элементов не найден!");
           return;
       }

       const educationPrograms = @json($educationPrograms);

       degreeSelect.addEventListener("change", function () {
           const selectedDegree = this.value;
           programSelect.innerHTML = '<option value="">Выберите программу</option>';

           educationPrograms.forEach(program => {
               if (program.degree === selectedDegree) {
                   const option = document.createElement("option");
                   option.value = program.id;
                   option.textContent = program.title;
                   programSelect.appendChild(option);
               }
           });
       });
   });
</script>

<script src="{{ asset('js/alert-pop-up.js') }}"></script>
@include('include.success-message')
@include('include.error-message')

@endsection
