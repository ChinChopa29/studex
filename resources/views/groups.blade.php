@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
   $filteredGroups = $groups->filter(fn($group) => empty($group->subgroup));
@endphp

@extends('layout.layout')

@section('title') 
Все группы
@endsection

@section('content')
@if($user)
<div x-data="studentFilter()" class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Хлебные крошки и заголовок -->
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex') }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">Все группы</span>
            </div>
            
            <div class="flex items-center justify-between">
               <div class="flex items-center space-x-4">
                  <h1 class="text-3xl font-bold">Все группы</h1>
               </div>
            </div>
        </div>

        <!-- Поле поиска -->
        <div class="mb-6">
            <input
                type="text"
                x-model="search"
                placeholder="Поиск студента по имени или email..."
                class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Основной контент -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-2xl font-semibold mb-6 flex items-center">
                <i class="fas fa-users mr-2 text-yellow-400"></i>
                Список всех групп
            </h2>

            @forelse($filteredGroups as $group)
            <div class="mb-8 bg-gray-700 p-6 rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-semibold">{{ $group->name }}</h3>
                        @if($group->description)
                        <p class="text-gray-400 text-sm mt-1">{{ $group->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm bg-gray-600 text-gray-300 px-3 py-1 rounded-full">
                            {{ $group->students->count() }} студентов
                        </span>
                    </div>
                </div>

                <!-- Подгруппы -->
                @if($group->subgroups->isNotEmpty())
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-300 mb-2">Подгруппы:</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($group->subgroups as $subgroup)
                        <div class="bg-gray-600 p-3 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span>{{ $subgroup->name }}</span>
                                <span class="text-xs bg-gray-500 text-gray-200 px-2 py-1 rounded-full">
                                    {{ $subgroup->students->count() }} студентов
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Студенты -->
                <div>
                    <h4 class="font-semibold text-gray-300 mb-3">Студенты:</h4>
                    @if($group->students->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
                        @foreach($group->students as $student)
                        <a href="{{ route('UserProfile', ['type' => 'student', 'id' => $student->id]) }}">
                            <div
                                x-show="matches('{{ strtolower($student->surname . ' ' . $student->name . ' ' . $student->lastname . ' ' . $student->email) }}')"
                                class="flex items-center space-x-3 bg-gray-600 p-3 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-500 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-300"></i>
                                    </div>
                                </div>
                                
                                    <div class="min-w-0">
                                        <p class="font-medium truncate">{{ $student->surname }} {{ $student->name }} {{ $student->lastname }}</p>
                                        <p class="text-sm text-gray-400 truncate">{{ $student->email }}</p>
                                    </div>     
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-400">В этой группе пока нет студентов</p>
                    @endif
                </div>
            </div>

            @empty
            <div class="bg-gray-700 p-6 rounded-lg text-center">
                <i class="fas fa-users-slash text-4xl text-gray-500 mb-4"></i>
                <h3 class="text-xl font-semibold">Нет созданных групп</h3>
                <p class="text-gray-400 mt-2">Создайте группы, чтобы они появились здесь</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function studentFilter() {
    return {
        search: '',
        matches(content) {
            return content.toLowerCase().includes(this.search.toLowerCase());
        }
    }
}
</script>
@endpush
