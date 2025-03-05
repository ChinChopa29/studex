@extends('layout.layout')
@section('title') 
Преподаватели
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
   <div class="flex items-center gap-4 mb-6">
        <a href="{{route('admin.index')}}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
            <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
        </a>
        <h1 class="text-2xl font-semibold flex items-center gap-2">
            <i class="fas fa-list text-2xl"></i> Преподаватели
        </h1>
   </div>

    <div>
        <form id="searchForm" action="{{ route('admin.searchTeacher') }}" method="get" class="flex items-center my-4 gap-2 w-full">
            @csrf
            <input name="search" type="search" class="text-black border-2 rounded-lg py-2 px-4 w-1/4 focus:ring focus:ring-blue-300 transition" placeholder="Поиск..." value="{{ request('search') }}">
            
            <button type="submit" class="bg-green-600 py-2.5 px-4 rounded-lg hover:bg-green-700 transition-all duration-200 w-12">
                <i class="fa fa-search text-xl"></i>
            </button>
        
            <a href="{{ route('admin.showTeachers') }}" class="bg-red-500 py-2.5 px-4 rounded-lg hover:bg-red-700 transition-all duration-200 w-12">
                <i class="fa fa-x text-xl"></i>
            </a>
        </form>
    </div>
   
   <table class="min-w-full border-collapse bg-slate-700 text-white shadow-lg rounded-lg overflow-hidden">
      <thead class="bg-slate-900">
          <tr>
              <th class="px-6 py-3 text-left">ФИО</th>
              <th class="px-6 py-3 text-left">ИИН</th>
              <th class="px-6 py-3 text-left">Корпоративная почта</th>
              <th class="px-6 py-3 text-center">Действия</th>
          </tr>
      </thead>
      <tbody>
         @forelse($teachers as $teacher)
            <tr class="border-t border-slate-600 hover:bg-slate-800 transition">
               <td class="px-6 py-4 w-1/4">{{ $teacher->name }} {{ $teacher->surname }} {{ $teacher->lastname }}</td>
               <td class="px-6 py-4 w-1/6">{{ $teacher->iin }}</td>
               <td class="px-6 py-4 w-1/4">{{ $teacher->email }}</td>
               <td class="px-6 py-4 text-center flex justify-center gap-2">
                    <a href="{{ route('admin.showTeacher', ['teacher' => $teacher->id]) }}" class="bg-slate-500 px-4 py-2 rounded-lg hover:bg-slate-700 transition flex items-center gap-2">
                        <i class="fa fa-info-circle"></i> <span>Просмотр</span>
                    </a>
                    <a href="{{ route('admin.editTeacher', ['teacher' => $teacher->id]) }}" class="bg-green-600 px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <i class="fa fa-edit"></i> <span>Редактировать</span>
                    </a>
                    <form action="{{ route('admin.destroyTeacher', ['teacher' => $teacher->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этого преподавателя?');">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="bg-red-500 px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                            <i class="fa fa-trash"></i> <span>Удалить</span>
                        </button>
                    </form>
                </td>
            </tr>
          @empty
          <tr>
              <td colspan="4" class="text-center border px-6 py-6 text-2xl text-gray-300">Преподаватели не найдены</td>
          </tr>
          @endforelse
      </tbody>
  </table>
  
   <div class="mt-6">
      {{ $teachers->links() }} 
   </div>
  
</div>
<script src="{{asset('js/alert-pop-up.js')}}"></script>
@include('include.success-message')
@endsection