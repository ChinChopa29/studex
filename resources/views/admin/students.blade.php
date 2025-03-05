@extends('layout.layout')
@section('title') 
Студенты
@endsection

@section('content')
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
    <div class="flex items-center gap-4 mb-6">
       <a href="{{route('admin.index')}}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
          <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
       </a>
       <h1 class="text-2xl font-semibold flex items-center gap-2">
          <i class="fas fa-list text-2xl"></i> Список студентов
       </h1>
    </div>

    <div>
        <form id="searchForm" action="{{ route('admin.searchUser') }}" method="get" class="flex items-center my-4 gap-2 w-full">
            @csrf
            <input name="search" type="search" class="text-black border-2 rounded-lg py-2 px-4 w-1/4 focus:ring focus:ring-blue-300 transition" placeholder="Поиск..." value="{{ request('search') }}">
            
            <button type="submit" class="bg-green-600 py-2.5 px-4 rounded-lg hover:bg-green-700 transition-all duration-200 w-12">
                <i class="fa fa-search text-xl"></i>
            </button>
        
            <a href="{{ route('admin.showUsers') }}" class="bg-red-500 py-2.5 px-4 rounded-lg hover:bg-red-700 transition-all duration-200 w-12">
                <i class="fa fa-x text-xl"></i>
            </a>
        </form>
    </div>

    <table class="min-w-full border-collapse bg-slate-700 text-white shadow-lg rounded-lg overflow-hidden">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-6 py-3 text-left">ФИО</th>
                <th class="px-6 py-3 text-left">ИИН</th>
                <th class="px-6 py-3 text-left">Группа</th>
                <th class="px-6 py-3 text-center">Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr class="border-t border-slate-600 hover:bg-slate-800 transition">
                <td class="px-6 py-4 w-1/4">{{ $student->name }} {{ $student->surname }} {{ $student->lastname }}</td>
                <td class="px-6 py-4 w-1/6">{{ $student->iin }}</td>
                <td class="px-6 py-4 w-1/4">{{ $student->groups->pluck('name')->join(', ') ?: 'Нет данных' }}</td>
                <td class="px-6 py-4 text-center flex justify-center gap-2">
                    <a href="{{ route('admin.showUser', ['student' => $student->id]) }}" class="bg-slate-500 px-4 py-2 rounded hover:bg-slate-700 transition flex items-center gap-2">
                        <i class="fa fa-info-circle"></i> <span>Просмотр</span>
                    </a>
                    <a href="{{ route('admin.editUser', ['student' => $student->id]) }}" class="bg-green-600 px-4 py-2 rounded hover:bg-green-700 transition flex items-center gap-2">
                        <i class="fa fa-edit"></i> <span>Редактировать</span>
                    </a>
                    <form action="{{ route('admin.destroyUser', ['student' => $student->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этого студента?');">
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
                <td colspan="4" class="text-center border px-4 py-4 text-2xl">Студенты не найдены</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-6">
        {{ $students->links() }}
    </div>
</div>
<script src="{{asset('js/alert-pop-up.js')}}"></script>
@include('include.success-message')
@endsection