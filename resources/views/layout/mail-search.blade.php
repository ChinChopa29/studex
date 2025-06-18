<div>
   <form id="searchForm" action="{{ route('mailSearch') }}" method="get" class="flex flex-wrap items-center gap-2 pl-1 mt-4 mb-6 w-full">
       @csrf

       <input type="hidden" name="mail_type" value="{{ $mail_type ?? request('mail_type') }}">

       <input name="search" type="search" 
              class="text-black border-2 rounded-lg py-2 px-4 w-full sm:w-1/4 focus:ring focus:ring-blue-300 transition" 
              placeholder="Поиск..." 
              value="{{ request('search') }}">

       <button type="submit" 
               class="bg-green-600 py-2.5 px-4 rounded-lg hover:bg-green-700 transition-all duration-200 w-12 flex justify-center items-center">
           <i class="fa fa-search text-xl"></i>
       </button>

       <a href="@stack('return-to')" 
          class="bg-red-500 py-2.5 px-4 rounded-lg hover:bg-red-700 transition-all duration-200 w-12 flex justify-center items-center">
         <i class="fa fa-x text-xl"></i>
       </a>
   </form>
</div>
