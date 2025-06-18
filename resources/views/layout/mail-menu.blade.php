<div class="bg-slate-800 text-white m-4 p-4 rounded-2xl shadow-lg flex flex-wrap gap-2 items-center justify-center
            sm:justify-start sm:gap-4 sm:p-6">
   <a href="{{route('mailCreate')}}"
      class="px-3 py-2 text-sm sm:text-base bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center gap-2 min-w-[140px] justify-center">
      <i class="fa fa-comments"></i> Написать сообщение
   </a>

   <a href="{{route('mailIndex')}}"
      class="px-3 py-2 text-sm sm:text-base bg-blue-500 rounded-lg hover:bg-blue-600 transition-all duration-200 flex items-center gap-2 min-w-[140px] justify-center">
      <i class="fa fa-archive"></i> Входящие
   </a>

   <a href="{{route('mailSended')}}"
      class="px-3 py-2 text-sm sm:text-base bg-purple-500 rounded-lg hover:bg-purple-600 transition-all duration-200 flex items-center gap-2 min-w-[140px] justify-center">
      <i class="fa fa-folder"></i> Посмотреть отправленные
   </a>

   <a href="{{route('mailFavorite')}}"
      class="px-3 py-2 text-sm sm:text-base bg-yellow-600 rounded-lg hover:bg-yellow-700 transition-all duration-200 flex items-center gap-2 min-w-[140px] justify-center">
      <i class="fa fa-star"></i> Избранное
   </a>

   <a href="{{route('mailRecentDeleted')}}"
      class="px-3 py-2 text-sm sm:text-base bg-red-500 rounded-lg hover:bg-red-600 transition-all duration-200 flex items-center gap-2 min-w-[140px] justify-center">
      <i class="fa fa-recycle"></i> Недавно удаленные
   </a>
</div>

 
<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">  
   <div class="flex items-center justify-between mb-8">
       <div class="flex flex-col">
         <h2 class="text-2xl font-bold">@yield('page-name')</h2>
         <hr class="border-b border-slate-600 w-full mt-2">
   </div>
</div>