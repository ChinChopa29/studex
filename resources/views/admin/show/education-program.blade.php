@extends('layout.layout')
@section('title') 
Образовательные программы
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
      <a href="{{ route('admin.showPrograms') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
         <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
      </a>
      <h1 class="text-2xl font-semibold flex items-center gap-2">
         <i class="fas fa-list text-2xl"></i> Образовательная программа "{{ $educationProgram->title }}"
      </h1>
   </div>

   @if($editing ?? false)
      <form action="{{ route('admin.updateProgram', $educationProgram->id) }}" method="POST" class="space-y-6">
         @csrf
         @method('PUT')

         @if ($errors->any())
            <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg">
               <ul class="space-y-1">
                  @foreach ($errors->all() as $error)
                     <li>• {{ $error }}</li>
                  @endforeach
               </ul>
            </div>
         @endif

         <!-- Название -->
         <div class="flex flex-col">
            <label for="title" class="text-lg font-medium">Название</label>
            <div class="flex items-center gap-3">
               <input type="text" id="title" name="title" value="{{ $educationProgram->title }}"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <span class="tooltip" data-tooltip="Название для образовательной программы">
                  <i class="fas fa-info-circle text-xl text-gray-400"></i>
               </span>
            </div>
         </div>

         <!-- Описание -->
         <div class="flex flex-col">
            <label for="description" class="text-lg font-medium">Описание</label>
            <div class="flex items-center gap-3">
               <textarea id="description" name="description"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $educationProgram->description }}</textarea>
               <span class="tooltip" data-tooltip="Введите описание образовательной программы">
                  <i class="fas fa-info-circle text-xl text-gray-400"></i>
               </span>
            </div>
         </div>

         <!-- Степень -->
         <div class="flex flex-col">
            <label for="degree" class="text-lg font-medium">Степень</label>
            <div class="flex items-center gap-3">
               <select id="degree" name="degree"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="Бакалавриат" {{ $educationProgram->degree == 'Бакалавриат' ? 'selected' : '' }}>Бакалавриат</option>
                  <option value="Магистратура" {{ $educationProgram->degree == 'Магистратура' ? 'selected' : '' }}>Магистратура</option>
                  <option value="Аспирантура" {{ $educationProgram->degree == 'Аспирантура' ? 'selected' : '' }}>Аспирантура</option>
               </select>
               <span class="tooltip" data-tooltip="Выберите степень обучения для данной образовательной программы">
                  <i class="fas fa-info-circle text-xl text-gray-400"></i>
               </span>
            </div>
         </div>

         <!-- Длительность обучения -->
         <div class="flex flex-col">
            <label for="duration" class="text-lg font-medium">Длительность обучения</label>
            <div class="flex items-center gap-3">
               <select id="duration" name="duration"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="4" {{ $educationProgram->duration == 4 ? 'selected' : '' }}>4 года</option>
                  <option value="3" {{ $educationProgram->duration == 3 ? 'selected' : '' }}>3 года</option>
               </select>
               <span class="tooltip" data-tooltip="Выберите длительность для данной образовательной программы из предложенных">
                  <i class="fas fa-info-circle text-xl text-gray-400"></i>
               </span>
            </div>
         </div>

         <!-- Форма обучения -->
         <div class="flex flex-col">
            <label for="mode" class="text-lg font-medium">Форма обучения</label>
            <div class="flex items-center gap-3">
               <select id="mode" name="mode"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="Очная" {{ $educationProgram->mode == 'Очная' ? 'selected' : '' }}>Очная</option>
                  <option value="Очно-заочная" {{ $educationProgram->mode == 'Очно-заочная' ? 'selected' : '' }}>Очно-заочная</option>
                  <option value="Дистанционная" {{ $educationProgram->mode == 'Дистанционная' ? 'selected' : '' }}>Дистанционная</option>
               </select>
               <span class="tooltip" data-tooltip="Выберите форму обучения для данной образовательной программы">
                  <i class="fas fa-info-circle text-xl text-gray-400"></i>
               </span>
            </div>
         </div>

         <!-- Стоимость обучения -->
         <div class="flex flex-col">
            <label for="price" class="text-lg font-medium">Стоимость обучения</label>
            <div class="flex items-center gap-3">
               <input type="number" id="price" name="price" value="{{ $educationProgram->price }}"
                  class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
               <span class="tooltip" data-tooltip="Введите стоимость обучения образовательной программы за один год">
                  <i class="fas fa-info-circle text-xl text-gray-400"></i>
               </span>
            </div>
         </div>

         <!-- Акроним -->
         <div class="flex flex-col">
            <label for="acronym" class="text-lg font-medium">Акроним</label>
            <div class="flex items-center gap-3">
               <input type="text" id="acronym" name="acronym" value="{{ $educationProgram->acronym }}" readonly
                  class="w-full md:w-1/3 bg-gray-300 text-black border-2 border-gray-300 rounded-lg py-2 px-4 focus:outline-none">
               <span class="tooltip" data-tooltip="Акроним (сокращение) будет составлен из первых букв каждого слова автоматически">
                  <i class="fas fa-info-circle text-xl text-gray-400"></i>
               </span>
            </div>
         </div>

         <!-- Кнопки -->
         <div class="flex flex-wrap gap-4">
            <button type="submit" class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-check"></i> Сохранить
            </button>
            <a href="{{ url()->previous() }}" class="w-full md:w-1/3 bg-red-500 hover:bg-red-700 transition-all duration-200 rounded-lg py-2 px-4 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-x"></i> Отмена
            </a>
         </div>
      </form>
   @else
      <div class="space-y-4">
         <div class="flex flex-col">
            <label class="text-lg font-medium">Название</label>
            <p class="text-gray-300">{{ $educationProgram->title }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Описание</label>
            <p class="text-gray-300">{{ $educationProgram->description }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Степень</label>
            <p class="text-gray-300">{{ $educationProgram->degree }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Длительность обучения</label>
            <p class="text-gray-300">{{ $educationProgram->duration }} 
               @if($educationProgram->duration === 1)
                  год
               @elseif ($educationProgram->duration === 5)
                  лет
               @else
                  года
               @endif
            </p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Форма обучения</label>
            <p class="text-gray-300">{{ $educationProgram->mode }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Стоимость обучения</label>
            <p class="text-gray-300">{{ $educationProgram->price }} тг.</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Акроним</label>
            <p class="text-gray-300">{{ $educationProgram->acronym }}</p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Дата создания</label>
            <p class="text-gray-300">{{ $educationProgram->created_at->format('d.m.Y') }} 
               <span class="text-sm text-gray-500">{{ $educationProgram->created_at->diffForHumans() }}</span>
            </p>
         </div>
         <div class="flex flex-col">
            <label class="text-lg font-medium">Дата последнего обновления</label>
            <p class="text-gray-300">{{ $educationProgram->updated_at->format('d.m.Y') }} 
               <span class="text-sm text-gray-500">{{ $educationProgram->updated_at->diffForHumans() }}</span>
            </p>
         </div>
      </div>

      <div class="flex flex-wrap gap-4 mt-6">
         <form action="{{ route('admin.editProgram', ['educationProgram' => $educationProgram->id]) }}" method="get">
            <button type="submit" class="min-w-48 w-full md:w-1/3 bg-blue-600 hover:bg-blue-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               <i class="fas fa-edit"></i> Редактировать
            </button>
         </form>
         <form action="{{ route('admin.destroyProgram', ['educationProgram' => $educationProgram->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить эту образовательную программу?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="min-w-48 w-full md:w-1/3 bg-red-500 hover:bg-red-700 transition-all duration-200 rounded-lg py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-trash"></i> Удалить
            </button>
        </form>
      </div>
   @endif
</div>

<script src="{{ asset('js/add-program.js') }}"></script>
<script src="{{ asset('js/alert-pop-up.js') }}"></script>
<script>
   function updateDurationOptions() {
       const degree = document.getElementById('degree').value;
       const durationSelect = document.getElementById('duration');
       let options = [];

       if (degree === 'Бакалавриат') {
           options = [
               { value: 3, label: '3 года' },
               { value: 4, label: '4 года' }
           ];
       } else if (degree === 'Магистратура') {
           options = [
               { value: 1, label: '1 год' },
               { value: 2, label: '2 года' }
           ];
       } else if (degree === 'Аспирантура') {
           options = [
               { value: 3, label: '3 года' },
               { value: 4, label: '4 года' },
               { value: 5, label: '5 лет' }
           ];
       }

       durationSelect.innerHTML = '';

       options.forEach(option => {
           const optionElement = document.createElement('option');
           optionElement.value = option.value;
           optionElement.textContent = option.label;
           durationSelect.appendChild(optionElement);
       });

       const currentDuration = '{{ $educationProgram->duration }}'; 
       if (currentDuration) {
           durationSelect.value = currentDuration;
       }
   }

   window.onload = updateDurationOptions;
</script>
@include('include.success-message')
@endsection