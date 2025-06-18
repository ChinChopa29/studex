@extends('layout.layout')

@section('title') 
    Профиль - {{ $user->name }}
@endsection

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="h-32 bg-gradient-to-r from-blue-600 to-purple-600"></div>
            
            <div class="px-6 pb-6 relative">
               <div class="absolute -top-16 left-6 w-32 h-32 border-4 border-gray-800 rounded-full overflow-hidden bg-gray-700 flex items-center justify-center text-5xl">
                  @if($user instanceof App\Models\Teacher && $user->image)
                      <img 
                          src="{{ asset('storage/' . $user->image) }}" 
                          alt="Аватар" 
                          class="w-full h-full object-cover">
                  @else
                      <i class="fas fa-user text-gray-400"></i>
                  @endif
               </div>
         
               <div class="pt-20 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                     <div>
                        <h1 class="text-2xl md:text-3xl font-bold">{{ $user->surname }} {{ $user->name }} {{ $user->lastname }}</h1>
                        <p class="text-gray-400 flex items-center mt-1">
                            <i class="fas fa-envelope mr-2"></i> {{ $user->email }}
                        </p>
                        @if($user instanceof App\Models\Student && $user->groups->isNotEmpty())
                           <p class="text-gray-400 flex items-center mt-1">
                              <i class="fas fa-users mr-2"></i>
                              {{ $user->groups->pluck('name')->join(', ') }}
                           </p>
                        @elseif($user instanceof App\Models\Student)
                           <p class="text-gray-400 flex items-center mt-1">
                              <i class="fas fa-users mr-2"></i>
                              Группа не указана
                           </p>
                        @endif

                     </div>
               </div>
            </div>
         </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-user-circle text-blue-400 mr-2"></i>
                        Основная информация
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-400 text-sm">Полное имя</p>
                            <p class="font-medium">{{ $user->surname }} {{ $user->name }} {{ $user->lastname }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Email</p>
                            <p class="font-medium">{{ $user->email }}</p>
                        </div>
                        @if(Auth::id() === $user->id)
                            @if($user->phone)
                            <div>
                                <p class="text-gray-400 text-sm">Телефон</p>
                                <p class="font-medium">{{ $user->phone }}</p>
                            </div>
                            @endif
                            @if($user->iin)
                            <div>
                                <p class="text-gray-400 text-sm">ИИН</p>
                                <p class="font-medium">{{ $user->iin }}</p>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>

                @if(Auth::id() === $user->id)
                <div class="bg-gray-800 rounded-xl shadow-lg p-6" x-data="{ open: false }">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-lock text-yellow-400 mr-2"></i>
                        Безопасность
                    </h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">Пароль</p>
                            </div>
                            <button @click="open = !open" type="button"
                                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                                Изменить пароль
                            </button>
                        </div>

                        <!-- Форма смены пароля -->
                        <form x-show="open" x-transition @click.away="open = false"
                            method="POST" action="{{ route('UpdatePassword', ['type' => $user->getType(), 'id' => $user->id]) }}"
                            class="mt-4 space-y-4 border-t border-gray-700 pt-4">
                            @csrf
                            @method('PUT')

                            <!-- Старый пароль -->
                            <div class="relative w-full">
                                <label for="current_password" class="block text-sm text-gray-400 mb-1">Старый пароль</label>
                                <div class="relative">
                                    <input type="password" name="password" id="current_password" required
                                        class="w-full px-3 py-2 pr-10 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="togglePassword('current_password', 'eyeIcon1')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center justify-center h-full">
                                        <i id="eyeIcon1" class="fa fa-eye text-gray-400"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Новый пароль -->
                            <div class="relative w-full">
                                <label for="new_password" class="block text-sm text-gray-400 mb-1">Новый пароль</label>
                                <div class="relative">
                                    <input type="password" name="new_password" id="new_password" required
                                        class="w-full px-3 py-2 pr-10 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="togglePassword('new_password', 'eyeIcon2')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center justify-center h-full">
                                        <i id="eyeIcon2" class="fa fa-eye text-gray-400"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Подтверждение пароля -->
                            <div class="relative w-full">
                                <label for="new_password_confirmation" class="block text-sm text-gray-400 mb-1">Повторите новый пароль</label>
                                <div class="relative">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                                        class="w-full px-3 py-2 pr-10 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="togglePassword('new_password_confirmation', 'eyeIcon3')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center justify-center h-full">
                                        <i id="eyeIcon3" class="fa fa-eye text-gray-400"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 transition-colors rounded-lg py-2 text-white font-semibold">
                                Сменить пароль
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-chart-line text-green-400 mr-2"></i>
                        Статистика
                    </h2>
                    <div class="space-y-4">
                        @if($user instanceof App\Models\Student)
                        <div>
                            <p class="text-gray-400 text-sm">Курсов</p>
                            <p class="font-medium text-2xl">{{ $user->courses->count() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Выполнено заданий</p>
                            <p class="font-medium text-2xl">{{ $user->grades->count() + $user->testResults->count() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Средний балл</p>
                            <p class="font-medium text-2xl">
                                @php
                                    $grades = $user->grades->pluck('grade')->merge($user->testResults->pluck('score'));
                                    echo $grades->isNotEmpty() ? round($grades->avg(), 2) : '—';
                                @endphp
                            </p>
                        </div>
                        @elseif($user instanceof App\Models\Teacher)
                        <div>
                            <p class="text-gray-400 text-sm">Курсов ведет</p>
                            <p class="font-medium text-2xl">{{ $user->courses->count() }}</p>
                        </div>
                        @endif
                    </div>
               </div>

               @if(Auth::id() === $user->id)
                  <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                     <h2 class="text-xl font-bold mb-4 flex items-center">
                           <i class="fas fa-bolt text-purple-400 mr-2"></i>
                           Быстрые действия
                     </h2>
                     <div class="space-y-3">
                           @if($user instanceof App\Models\Student)
                           <a href="{{ route('CoursesIndex') }}" class="flex items-center px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                              <i class="fas fa-book mr-3 text-blue-400"></i>
                              <span>Мои курсы</span>
                           </a>
                           @elseif($user instanceof App\Models\Teacher)
                           <a href="{{ route('CoursesIndex') }}" class="flex items-center px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                              <i class="fas fa-book mr-3 text-blue-400"></i>
                              <span>Мои курсы</span>
                           </a>
                           @endif
                     </div>
                  </div>
               @endif
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/toggle-pass.js') }}"></script>
@endif
@include('include.success-message')
@include('include.error-message') 
@endsection