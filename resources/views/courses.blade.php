@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
Курсы
@endsection


@section('content')
<div class="flex text-white m-4 ">
   <div class="bg-gradient-to-r from-blue-600 to-purple-600 w-full p-6 rounded-2xl shadow-lg">
      <h1 class="font-bold text-xl">Добро пожаловать в STUDEX!</h1>
      <h2>Ваша платформа для онлайн обучения</h2>
   </div>
</div>

@if($user) 
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
    @php
        $visibleCourses = $courses;

        if ($user instanceof \App\Models\Student) {
            $visibleCourses = $courses->filter(function ($course) use ($user) {
                return $user->courses->where('pivot.status', 'accepted')->contains($course);
            });
        }
    @endphp

    @if ($visibleCourses->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($visibleCourses as $course)
                <div 
                    x-data="{ showModal: false, selectedColor: '{{ $course->style->color ?? '#000000' }}' }"
                    class="relative">
                    <div 
                        class="p-6 rounded-2xl shadow-lg transition-transform transform hover:scale-105 text-white cursor-pointer"
                        style="background-color: {{ $course->style->color ?? '#3b82f6' }};" 
                        @click="window.location.href = '{{ route('CourseShow', ['course' => $course->id]) }}'">
                        <div @click.stop>
                            <a href="{{route('CourseShow', ['course' => $course->id])}}">
                            <h2 class="text-xl font-bold mb-2">{{ $course->name }}</h2>

                            @if(Auth::guard('student')->user())
                                <!-- Кнопка настройки -->
                                <button 
                                    @click="showModal = !showModal"
                                    class="absolute top-4 right-4 text-white hover:text-gray-200 transition z-20">
                                    <i class="fas fa-cog text-xl"></i>
                                </button>

                                <!-- Модалка -->
                                <div 
                                    x-show="showModal" 
                                    @click.outside="showModal = false"
                                    x-transition
                                    x-cloak
                                    class="absolute top-12 right-4 bg-white text-black p-4 rounded-xl shadow-lg w-64 z-30"
                                    @click.stop>
                                    <form 
                                        action="{{ route('ChangeCourseColor') }}" 
                                        method="POST"
                                        @submit="showModal = false">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">

                                        <label class="block text-sm font-medium mb-2">Выберите цвет</label>
                                        <input 
                                            type="color" 
                                            name="color" 
                                            x-model="selectedColor"
                                            class="w-full h-10 border rounded mb-4">

                                        <div class="flex justify-end space-x-2">
                                            <button 
                                                type="button" 
                                                @click="showModal = false"
                                                class="px-3 py-1 bg-gray-200 text-sm rounded hover:bg-gray-300">
                                                Отмена
                                            </button>
                                            <button 
                                                type="submit"
                                                class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                                Сохранить
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400">У вас нет назначенных курсов.</p>
    @endif
</div>
@endif
<style>
    [x-cloak] { display: none !important; }
</style>
@include('include.success-message')
@include('include.error-message') 
@endsection