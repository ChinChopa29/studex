@extends('layout.layout')
@section('title') 
Создание подгрупп
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{route('admin.index')}}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Создание подгруппы
      </h1>
   </div>

   <form action="{{route('admin.storeSubgroup')}}" method="post" class="space-y-6">
      @csrf

      <!-- Название подгруппы -->
      <div class="flex flex-col">
         <label for="acronym" class="text-lg font-medium">Название подгруппы</label>
         <div class="flex items-center gap-3">
            <input type="text" id="acronym" name="name" placeholder="Введите название подгруппы"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
               value="">
            <span class="tooltip" data-tooltip="Название подгруппы должно быть уникальным">
               <i class="fas fa-info-circle text-xl text-gray-400"></i>
            </span>
         </div>
      </div>

      <!-- Родительская группа -->
      <div class="flex flex-col">
         <label for="group" class="text-lg font-medium">Родительская группа</label>
         <div class="flex items-center gap-3">
            <select name="group" id="group"
               class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               @foreach ($groups as $group)
                  @if($group->subgroup === null)
                     <option value="{{ $group->id }}" {{ old('group') == $group->id ? 'selected' : '' }}>
                           {{ $group->name }}
                     </option>
                  @endif
               @endforeach
            </select>
            <span class="tooltip" data-tooltip="Выберите родительскую группу">
               <i class="fas fa-info-circle text-xl text-gray-400"></i>
            </span>
         </div>
      </div>
      <div id="acronym-check-result" class="mt-4"></div>
      <!-- Кнопки -->
      <div class="flex flex-wrap gap-4">
         <button class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i> Создать подгруппу
         </button>
         <a href="{{route('admin.createGroup')}}"
            class="w-full md:w-1/3 bg-blue-600 hover:bg-blue-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
            <i class="fas fa-edit"></i> Создать группу
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
    const programSelect = document.getElementById("group");
    const acronymCheckResult = document.getElementById("acronym-check-result");

    function generateAcronym() {
        let selectedGroup = programSelect.options[programSelect.selectedIndex]?.text.trim();

        if (!selectedGroup) return;

        let acronym = selectedGroup + "/1";
        
        acronymInput.value = acronym;

        checkAcronymExistence(acronym);
    }

    function checkAcronymExistence(acronym) {
        if (!acronym.trim()) return;

        fetch(`/admin/group/search-acronym-subgroup?acronym=${encodeURIComponent(acronym)}`)
            .then(response => response.json())
            .then(data => {
               if (data.exists) {
                  acronymCheckResult.innerHTML = `<span class="text-red-500">Группа с таким названием уже существует. <a href="/admin/groups/${data.id}" class="text-blue-500">${data.name}</a></span>`;
               } else {
                  acronymCheckResult.innerHTML = `<span class="text-green-500">Название свободно, можно использовать.</span>`;
               }

            })
            .catch(error => {
                console.error("Ошибка при проверке акронима:", error);
                acronymCheckResult.innerHTML = '<span class="text-red-500">Произошла ошибка при проверке. Попробуйте снова.</span>';
            });
    }
    programSelect.addEventListener("change", generateAcronym);

    acronymInput.addEventListener("input", function() {
        const acronym = acronymInput.value.trim();
        checkAcronymExistence(acronym);
    });

    generateAcronym();
});

</script>
@include('include.success-message')
@endsection



