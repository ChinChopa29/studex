@extends('layout.layout')
@section('title') 
Создание групп
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{route('admin.index')}}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Создание группы
      </h1>
   </div>

   <form action="{{route('admin.storeGroup')}}" method="post" class="space-y-6">
      @csrf

      <!-- Название группы -->
      <div class="flex flex-col">
         <label for="acronym" class="text-lg font-medium">Название группы</label>
         <div class="flex items-center gap-3">
            <input type="text" id="acronym" name="name" placeholder="Введите название группы"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
               value="">
            <span class="tooltip" data-tooltip="Название группы должно быть уникальным">
               <i class="fas fa-info-circle text-xl text-gray-400"></i>
            </span>
         </div>
      </div>

      <!-- Год поступления -->
      <div class="flex flex-col">
         <label for="admission_year" class="text-lg font-medium">Год поступления</label>
         <div class="flex items-center gap-3">
            <input type="number" id="admission_year" name="admission_year" placeholder="Введите год"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
               value="2025">
            <span class="tooltip" data-tooltip="Год поступления, год выпуска присвоится автоматически">
               <i class="fas fa-info-circle text-xl text-gray-400"></i>
            </span>
         </div>
      </div>

      <!-- Образовательная программа -->
      <div class="flex flex-col">
         <label for="education_program_id" class="text-lg font-medium">Образовательная программа</label>
         <div class="flex items-center gap-3">
            <select name="education_program_id" id="education_program_id" onchange="generateAcronym()"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               @foreach ($educationPrograms as $educationProgram)
                  <option value="{{ $educationProgram->id }}" {{ old('education_program_id') == $educationProgram->id ? 'selected' : '' }}>
                     {{ $educationProgram->title }}
                  </option>
               @endforeach
            </select>
            <span class="tooltip" data-tooltip="Выберите образовательную программу">
               <i class="fas fa-info-circle text-xl text-gray-400"></i>
            </span>
         </div>
      </div>
      <div id="acronym-check-result" class="mt-4"></div>
      <!-- Кнопки -->
      <div class="flex flex-wrap gap-4">
         <button class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i> Создать группу
         </button>
         <a href="{{route('admin.createSubgroup')}}"
            class="w-full md:w-1/3 bg-blue-600 hover:bg-blue-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
            <i class="fas fa-edit"></i> Создать подгруппу
         </a>
      </div>
   </form>

   <!-- Сообщения об ошибках -->
   @if ($errors->any())
      <div class="bg-red-500 text-white p-4 rounded-lg mt-6 shadow-lg">
         <ul class="space-y-1">
            @foreach ($errors->all() as $error)
               <li>• {{ $error }}</li>
            @endforeach
         </ul>
      </div>
   @endif
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const acronymInput = document.getElementById("acronym");
    const programSelect = document.getElementById("education_program_id");
    const yearInput = document.getElementById("admission_year");
    const acronymCheckResult = document.getElementById("acronym-check-result");

    let isManualInput = false; 

    function generateAcronym() {
        if (isManualInput) return; 

        let selectedProgram = programSelect.options[programSelect.selectedIndex]?.text.trim();
        let year = yearInput.value.trim();

        if (!selectedProgram || !year) return;

        let acronym = selectedProgram.split(' ').map(word => word[0]).join('').toUpperCase() + year + '-a';

        acronymInput.value = acronym;
        checkAcronymExistence(acronym);  
    }

    function checkAcronymExistence(acronym) {
        if (!acronym.trim()) return; 

        fetch(`/admin/group/search-acronym?acronym=${acronym}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let listHTML = '<span class="text-red-500">Этот акроним уже занят. Используйте другой. Существующие группы:</span><ul>';
                    data.forEach(group => {
                        listHTML += `<li><a href="/admin/groups/${group.id}" class="text-blue-500 hover:text-blue-700">${group.name}</a></li>`;
                    });
                    listHTML += '</ul>';
                    acronymCheckResult.innerHTML = listHTML;
                } else {
                    acronymCheckResult.innerHTML = '<span class="text-green-500">Название свободно!</span>';
                }
            })
            .catch(error => {
                console.error("Ошибка при проверке акронима:", error);
                acronymCheckResult.innerHTML = '<span class="text-red-500">Произошла ошибка при проверке. Попробуйте снова.</span>';
            });
    }

    acronymInput.addEventListener("input", function() {
        isManualInput = true; 
        checkAcronymExistence(acronymInput.value);  
    });

    programSelect.addEventListener("change", () => {
        isManualInput = false; 
        generateAcronym();
    });

    yearInput.addEventListener("input", () => {
        isManualInput = false; 
        generateAcronym();
    });

    generateAcronym();
});
</script>
@include('include.success-message')
@endsection
