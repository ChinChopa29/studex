@extends('layout.layout')
@section('title') 
Студенты
@endsection

@section('content')
<div class="text-white bg-slate-800 m-4 px-4 py-4 rounded-lg">
   <div class="flex items-center gap-4 mb-8">
        <a href="{{route('admin.index')}}"><i class="fa fa-arrow-left text-2xl hover:text-gray-400 hover:border-gray-400 transition-all duration-200 border-2 rounded-full p-1"></i></a>
        <h1 class="text-2xl"><i class="fas fa-list text-2xl "></i> Студенты</h1>
   </div>

    <div>
        <form id="searchForm" action="{{ route('admin.searchUser') }}" method="get" class="flex items-center my-4 gap-2 w-full">
            @csrf
            <input name="search" type="search" class="text-black border-2 rounded-lg py-2 px-4 w-1/4" placeholder="Поиск..." value="{{ request('search') }}">
            
            <button type="submit" class="bg-green-600 py-2.5 px-4 rounded-lg hover:bg-green-700 transition-all duration-200 w-12">
                <i class="fa fa-search text-xl"></i>
            </button>
        
            <a href="{{ route('admin.showUsers') }}" class="bg-red-500 py-2.5 px-4 rounded-lg hover:bg-red-700 transition-all duration-200 w-12">
                <i class="fa fa-x text-xl"></i>
            </a>
        </form>
    </div>
   
   
   <table class="min-w-full border-collapse border ">
      <thead>
          <tr>
              <th class="border border-slate-500 px-4 py-2">ФИО</th>
              <th class="border border-slate-500 px-4 py-2">ИИН</th>
              <th class="border border-slate-500 px-4 py-2">Группа</th>
              <th class="border border-slate-500 px-4 py-2">Действия</th>
          </tr>
      </thead>
      <tbody>
         @forelse($students as $student)
            <tr>
               <td class="border border-slate-500 px-4 py-2 w-1/6">{{ $student->name }} {{ $student->surname }} {{ $student->lastname }}</td>
               <td class="border border-slate-500 px-4 py-2 w-1/12">{{ $student->iin }}</td>
               <td class="border border-slate-500 px-4 py-2 w-1/12">
               {{ $student->groups->pluck('name')->join(', ') ?: 'Нет данных' }}
               </td>
               <td class="border border-slate-500 px-4 py-2 w-1/12 text-center">
                     <div class="flex justify-center gap-2">
                        <form action="{{ route('admin.showUser', ['student' => $student->id]) }}" method="get">
                           @csrf
                           <button type="submit" class="bg-slate-500 text-white px-4 py-2 rounded hover:bg-slate-700 transition">
                              <i class="fa fa-info-circle text-xl"></i>
                           </button>
                        </form>
   
                        <form action="{{ route('admin.editUser', ['student' => $student->id]) }}" method="get">
                           @csrf
                           <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                              <i class="fa fa-edit text-xl"></i>
                           </button>
                        </form>
   
                        <form action="{{ route('admin.destroyUser', ['student' => $student->id]) }}" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить этого студента?');">
                           @method('DELETE')
                           @csrf
                           <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                 <i class="fa fa-trash text-xl"></i>
                           </button>
                        </form>
                     </div>
               </td>
          </tr>
          @empty
          <tr>
              <td colspan="5" class="text-center border px-4 py-4 text-2xl">Студенты не найдены</td>
          </tr>
          @endforelse
      </tbody>
  </table>
   <div class="mt-4">
      {{ $students->links() }} 
   </div>
  
</div>
<script src="{{asset('js/alert-pop-up.js')}}"></script>
@include('include.success-message')
@endsection