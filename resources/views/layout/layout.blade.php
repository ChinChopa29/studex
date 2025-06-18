<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    @vite('resources/css/app.css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    @yield('head')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-900">

   @php
      $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
   @endphp
   
   <div x-data="{ open: false }" class="flex min-h-screen relative">
   
      {{-- Боковое меню для больших экранов --}}
      <div class="hidden md:flex flex-col h-screen sticky top-0 left-0 w-56 bg-slate-800 text-white shadow-xl z-10">
         @include('layout.sidebar')
      </div>
   
      {{-- Боковое меню (мобильное) --}}
      <div
         x-show="open"
         x-transition
         class="fixed inset-0 z-30 bg-black bg-opacity-50 md:hidden"
         @click="open = false">
         <div
            class="absolute left-0 top-0 w-64 h-full bg-slate-800 shadow-xl text-white"
            @click.stop>
            @include('layout.sidebar')
         </div>
      </div>
   
      {{-- Контент + кнопка на мобильной версии --}}
      <div class="flex-1 w-full relative">
   
         {{-- Кнопка открытия меню на мобилке --}}
         <div class="md:hidden p-4">
            <button @click="open = true" class="text-white">
               <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"></path>
               </svg>
            </button>
         </div>
   
         {{-- Основной контент --}}
         <div class="p-4">
            @yield('content')
         </div>
   
      </div>
   </div>
   <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
   @stack('scripts')
</body>
   

{{-- Подсказки --}}
<style>
   .tooltip {
     position: relative;
     display: inline-block;
     cursor: pointer;
   }

   .tooltip::after {
     content: attr(data-tooltip);
     position: absolute;
     bottom: 150%;
     left: 50%;
     transform: translateX(-50%);
     background-color: black;
     color: white;
     padding: 5px 10px;
     border-radius: 5px;
     font-size: 12px;
     white-space: nowrap;
     opacity: 0;
     visibility: hidden;
     transition: opacity 0.3s;
   }

   .tooltip:hover::after {
     opacity: 1;
     visibility: visible;
   }
</style>
</html>