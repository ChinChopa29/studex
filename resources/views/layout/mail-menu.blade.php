<div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg flex items-center gap-4">
   <a href="{{route('mailCreate')}}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200">
      <i class="fa fa-comments"></i> Написать сообщение
   </a>

   <a href="{{route('mailIndex')}}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200">
      <i class="fa fa-archive"></i> Входящие
   </a>

   <a href="{{route('mailSended')}}" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-all duration-200">
      <i class="fa fa-folder"></i> Посмотреть отправленные
   </a>

   <a href="{{route('mailFavorite')}}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-200">
      <i class="fa fa-star"></i> Избранное
   </a>

   <a href="{{route('mailRecentDeleted')}}" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200">
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