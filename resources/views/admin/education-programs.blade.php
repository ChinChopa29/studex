@extends('layout.layout')
@section('title') 
Образовательные программы
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
    <div class="flex items-center gap-4 mb-6">
       <a href="{{route('admin.index')}}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
          <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
       </a>
       <h1 class="text-2xl font-semibold flex items-center gap-2">
          <i class="fas fa-list text-2xl"></i> Список образовательных программ
       </h1>
    </div>

    <div>
        <form id="searchForm" action="{{ route('admin.searchProgram') }}" method="get" class="flex items-center my-4 gap-2 w-full">
            @csrf
            <input name="search" type="search" class="text-black border-2 rounded-lg py-2 px-4 w-1/4 focus:ring focus:ring-blue-300 transition" placeholder="Поиск..." value="{{ request('search') }}">
        
            <select name="duration" class="py-2.5 px-4 rounded-lg text-black w-1/6">
                <option value="">Все длительности</option>
                <option value="1" {{ request('duration') == '1' ? 'selected' : '' }}>1 год</option>
                <option value="2" {{ request('duration') == '2' ? 'selected' : '' }}>2 года</option>
                <option value="3" {{ request('duration') == '3' ? 'selected' : '' }}>3 года</option>
                <option value="4" {{ request('duration') == '4' ? 'selected' : '' }}>4 года</option>
                <option value="5" {{ request('duration') == '5' ? 'selected' : '' }}>5 лет</option>
            </select>
        
            <select name="degree" class="py-2.5 px-4 rounded-lg text-black w-1/6">
                <option value="">Все степени</option>
                <option value="Бакалавриат" {{ request('degree') == 'Бакалавриат' ? 'selected' : '' }}>Бакалавриат</option>
                <option value="Магистратура" {{ request('degree') == 'Магистратура' ? 'selected' : '' }}>Магистратура</option>
                <option value="Аспирантура" {{ request('degree') == 'Аспирантура' ? 'selected' : '' }}>Аспирантура</option>
            </select>
        
            <select name="mode" class="py-2.5 px-4 rounded-lg text-black w-1/6">
                <option value="">Все формы обучения</option>
                <option value="Очная" {{ request('mode') == 'Очная' ? 'selected' : '' }}>Очная</option>
                <option value="Очно-заочная" {{ request('mode') == 'Очно-заочная' ? 'selected' : '' }}>Очно-заочная</option>
                <option value="Дистанционная" {{ request('mode') == 'Дистанционная' ? 'selected' : '' }}>Дистанционная</option>
            </select>
            
            <button type="submit" class="bg-green-600 py-2.5 px-4 rounded-lg hover:bg-green-700 transition-all duration-200 w-12">
                <i class="fa fa-search text-xl"></i>
            </button>
        
            <a href="{{ route('admin.showPrograms') }}" class="bg-red-500 py-2.5 px-4 rounded-lg hover:bg-red-700 transition-all duration-200 w-12">
                <i class="fa fa-x text-xl"></i>
            </a>
        </form>
    </div>
   
   
   <table class="min-w-full border-collapse bg-slate-700 text-white shadow-lg rounded-lg overflow-hidden">
      <thead class="bg-slate-900">
          <tr>
              <th class="px-6 py-3 text-left">Название</th>
              <th class="px-6 py-3 text-left">Длительность</th>
              <th class="px-6 py-3 text-left">Степень</th>
              <th class="px-6 py-3 text-left">Форма обучения</th>
              <th class="px-6 py-3 text-center">Действия</th>
          </tr>
      </thead>
      <tbody>
          @forelse($educationPrograms as $program)
          <tr class="border-t border-slate-600 hover:bg-slate-800 transition">
              <td class="px-6 py-4 w-1/4">{{ $program->title }}</td>
              <td class="px-6 py-4 w-1/6">{{ $program->duration }}
                  @if($program->duration === 1)
                      год
                  @elseif ($program->duration === 5)
                      лет
                  @else
                      года
                  @endif
              </td>
              <td class="px-6 py-4 w-1/6">{{ $program->degree }}</td>
              <td class="px-6 py-4 w-1/6">{{ $program->mode }}</td>
              <td class="px-6 py-4 text-center flex justify-center gap-2">
                  <a href="{{ route('admin.showProgram', ['educationProgram' => $program->id]) }}" class="bg-slate-500 px-4 py-2 rounded hover:bg-slate-700 transition flex items-center gap-2">
                      <i class="fa fa-info-circle"></i> <span>Просмотр</span>
                  </a>
                  <a href="{{ route('admin.editProgram', ['educationProgram' => $program->id]) }}" class="bg-green-600 px-4 py-2 rounded hover:bg-green-700 transition flex items-center gap-2">
                      <i class="fa fa-edit"></i> <span>Редактировать</span>
                  </a>
                  <form action="{{ route('admin.destroyProgram', ['educationProgram' => $program->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить эту программу?');">
                      @method('DELETE')
                      @csrf
                      <button type="submit" class="bg-red-500 px-4 py-2 rounded hover:bg-red-700 transition flex items-center gap-2">
                          <i class="fa fa-trash"></i> <span>Удалить</span>
                      </button>
                  </form>
              </td>
          </tr>
          @empty
          <tr>
              <td colspan="5" class="text-center border px-4 py-4 text-2xl">Образовательные программы не найдены</td>
          </tr>
          @endforelse
      </tbody>
  </table>
  <div class="mt-6">
    {{ $educationPrograms->appends(request()->query())->links() }}
  </div> 
  
</div>
<script src="{{asset('js/alert-pop-up.js')}}"></script>
@include('include.success-message')
@endsection