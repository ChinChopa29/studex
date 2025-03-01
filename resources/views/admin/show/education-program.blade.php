@extends('layout.layout')
@section('title') 
Образовательные программы
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 px-4 py-4 rounded-lg">
   <div class="flex items-center gap-4 mb-8">
      <a href="{{route('admin.showPrograms')}}"><i class="fa fa-arrow-left text-2xl hover:text-gray-400 hover:border-gray-400 transition-all duration-200 border-2 rounded-full p-1"></i></a>
      <h1 class="text-2xl"><i class="fas fa-list text-2xl "></i> Образовательная программа, подробнее</h1>
 </div>

   <div class="text-xl">
      @if($editing ?? false)
         <form action="{{ route('admin.updateProgram', $educationProgram->id) }}" method="POST">
            @csrf
            @method('PUT') 
      @endif

      <table class="min-w-full table-auto border-collapse border border-slate-600">
         <tbody>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Наименование:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  @if($editing ?? false)
                     <div class="flex items-center gap-4">
                        <span class="tooltip" data-tooltip="Название для образовательной программы">
                           <i class="fas fa-info-circle text-2xl"></i>
                        </span>
                        <input type="text" id="title" name="title" placeholder="Название" class="text-black border-2 rounded-lg py-2 px-4 transition-all duration-200 w-full md:w-1/3" value="{{$educationProgram->title}}">
                     </div>
                  @else
                     {{ $educationProgram->title }}
                  @endif
               </td>
            </tr>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Описание:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  @if($editing ?? false)
                  <div class="flex items-center gap-4">
                     <span class="tooltip" data-tooltip="Введите описание образовательной программы">
                        <i class="fas fa-info-circle text-2xl"></i>
                     </span>
                     <textarea name="description" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3 h-24" placeholder="Описание">{{ $educationProgram->description }}</textarea>
                  </div>
                  @else
                     {{ $educationProgram->description }}
                  @endif
               </td>
            </tr>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Степень:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  @if($editing ?? false)
                     <div class="flex items-center gap-4">
                        <span class="tooltip" data-tooltip="Выберите степень обучения для данной образовательной программы">
                           <i class="fas fa-info-circle text-2xl"></i>
                        </span>
                        <select id="degree" name="degree" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
                           <option value="Бакалавриат" @selected($educationProgram->degree == 'Бакалавриат')>Бакалавриат</option>
                           <option value="Магистратура" @selected($educationProgram->degree == 'Магистратура')>Магистратура</option>
                           <option value="Аспирантура" @selected($educationProgram->degree == 'Аспирантура')>Аспирантура</option>
                        </select>
                     </div>
                  @else
                     {{ $educationProgram->degree }}
                  @endif
               </td>
            </tr>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Длительность обучения:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  @if($editing ?? false)
                     <div class="flex items-center gap-4">
                        <span class="tooltip" data-tooltip="Выберите длительность для данной образовательной программы из предложенных">
                           <i class="fas fa-info-circle text-2xl"></i>
                        </span>
                        <select id="duration" name="duration" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
                           <option value="4" @selected($educationProgram->duration == 4)>4 года</option>
                           <option value="3" @selected($educationProgram->duration == 3)>3 года</option>
                        </select>
                     </div>
                  @else
                     {{ $educationProgram->duration }} 
                     @if($educationProgram->duration === 1)
                        год
                     @elseif ($educationProgram->duration === 5)
                        лет
                     @else
                        года
                     @endif
                  @endif
               </td>
            </tr>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Форма обучения:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  @if($editing ?? false)
                     <div class="flex items-center gap-4">
                        <span class="tooltip" data-tooltip="Выберите форму обучения для данной образовательной программы">
                           <i class="fas fa-info-circle text-2xl"></i>
                        </span>
                        <select name="mode" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3">
                           <option value="Очная" @selected($educationProgram->mode == 'Очная')>Очная</option>
                           <option value="Очно-заочная" @selected($educationProgram->mode == 'Очно-заочная')>Очно-заочная</option>
                           <option value="Дистанционная" @selected($educationProgram->mode == 'Дистанционная')>Дистанционная</option>
                        </select>
                     </div>
                  @else
                     {{ $educationProgram->mode }}
                  @endif
               </td>
            </tr>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Стоимость обучения:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  @if($editing ?? false)
                     <div class="flex items-center gap-4">
                        <span class="tooltip" data-tooltip="Введите стоимость обучения образовательной программы за один год">
                           <i class="fas fa-info-circle text-2xl"></i>
                        </span>
                        <input name="price" type="number" class="text-black border-2 rounded-lg py-2 px-4 w-full md:w-1/3" placeholder="Стоимость" min="0" step="0.01" value="{{$educationProgram->price}}"></input>
                     </div>
                  @else
                     {{ $educationProgram->price }} тг.
                  @endif
               </td>
            </tr>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Акроним:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  @if($editing ?? false)
                     <div class="flex items-center gap-4">
                        <span class="tooltip" data-tooltip="Акроним (сокращение) будет составлен из первых букв каждого слова автоматически">
                           <i class="fas fa-info-circle text-2xl"></i>
                        </span>
                        <input type="text" id="acronym" name="acronym" placeholder="Акроним" readonly 
                        class="text-black border-2 bg-gray-200 rounded-lg py-2 px-4 outline-none cursor-not-allowed w-full md:w-1/3" value="{{$educationProgram->acronym}}">
                     </div>
                  @else
                     {{ $educationProgram->acronym }}
                  @endif
               </td>
            </tr>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Дата создания:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  {{ $educationProgram->created_at->format('d.m.Y') }} 
                  <span class="text-sm text-gray-500">{{ $educationProgram->created_at->diffForHumans() }}</span>
               </td>
            </tr>
            <tr>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-semibold">Дата последнего обновления:</td>
               <td class="border-b-2 border-slate-600 px-4 py-4 font-normal">
                  {{ $educationProgram->created_at->format('d.m.Y') }} 
                  <span class="text-sm text-gray-500">{{ $educationProgram->updated_at->diffForHumans() }}</span>
               </td>
            </tr>
         </tbody>
      </table>

      @if($editing ?? false)
         <div class="mt-4 flex gap-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 duration-200 transition-all"><i class="fa fa-check text-xl"></i> Сохранить</button>
               <a href="{{url()->previous()}}" type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-700 duration-200 transition-all"><i class="fa fa-x text-xl"></i> Отмена</a>
         </div>
         </form>
      @else
         <div class="flex items-center gap-4 mt-4">
            <form action="{{ route('admin.editProgram', ['educationProgram' => $educationProgram->id]) }}" method="get">
               @csrf
               <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                  <i class="fa fa-edit text-xl"></i> Редактировать 
               </button>
            </form>
            <form action="{{ route('admin.destroyProgram', ['educationProgram' => $educationProgram->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить эту программу?');">
               @method('DELETE')
               @csrf
               <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                  <i class="fa fa-trash text-xl"></i> Удалить
               </button>
         </form>
         </div>
      @endif
      @if ($errors->any())
         <div class="bg-red-500 text-white p-4 rounded-lg mt-4">
            <ul>
               @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
      @endif
   </div>
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
