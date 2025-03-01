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
</head>
<body class="bg-slate-900">
   <div class="flex">
      {{-- Боковое меню --}}
      <div class="bg-slate-800 w-[12%] text-white h-screen sticky top-0 left-0">
         @include('layout.sidebar')
      </div>

      {{-- Основной контент страницы --}}
      <div class="w-full">
         @yield('content')
      </div>
   </div>
    
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