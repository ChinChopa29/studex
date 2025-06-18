@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

<div class="text-white h-screen flex flex-col justify-between bg-gradient-to-b from-gray-800 to-gray-900 border-r border-gray-700 shadow-xl">
   <!-- Логотип и основное меню -->
   <div class="mt-6 flex flex-col gap-1 px-2">
      <a href="{{ route('CoursesIndex') }}" class="font-bold text-2xl px-4 py-3 text-center bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-500">
         STUDEX
      </a>
      
      @php
         $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
      @endphp
      <div class="flex flex-col gap-1 mt-2">
         @if($user && method_exists($user, 'getType') && in_array($user->getType(), ['student', 'teacher']))
            <a href="{{ route('UserProfile', ['type' => $user->getType(), 'id' => $user->id]) }}" class="group">
               <div class="flex items-center gap-3 hover:bg-gray-700/50 transition-all duration-200 rounded-lg px-4 py-3 md:py-2.5 group-active:scale-95">
                     <div class="p-1.5 bg-gray-700/50 group-hover:bg-blue-500 rounded-lg transition-colors duration-200 flex items-center justify-center w-9 h-9">
                        <img src="{{asset('img/logos/male.png')}}" alt="" class="w-5 h-5 opacity-90 group-hover:opacity-100 object-contain">
                     </div>
                     <span class="font-medium group-hover:text-blue-300 text-sm md:text-base">Аккаунт</span>
               </div>
            </a>
         @endif
         
         <a href="{{ route('CoursesIndex') }}" class="group">
            <div class="flex items-center gap-3 hover:bg-gray-700/50 transition-all duration-200 rounded-lg px-4 py-3 md:py-2.5 group-active:scale-95">
               <div class="p-1.5 bg-gray-700/50 group-hover:bg-purple-500 rounded-lg transition-colors duration-200 flex items-center justify-center w-9 h-9">
                  <img src="{{ asset('img/logos/book.png') }}" alt="" class="w-5 h-5 opacity-90 group-hover:opacity-100 object-contain">
               </div>
               <span class="font-medium group-hover:text-purple-300 text-sm md:text-base">Курсы</span>
            </div>
         </a>
         
         <a href="{{route('showGroups')}}" class="group">
            <div class="flex items-center gap-3 hover:bg-gray-700/50 transition-all duration-200 rounded-lg px-4 py-3 md:py-2.5 group-active:scale-95">
               <div class="p-1.5 bg-gray-700/50 group-hover:bg-green-500 rounded-lg transition-colors duration-200 flex items-center justify-center w-9 h-9">
                  <img src="{{asset('img/logos/people.png')}}" alt="" class="w-5 h-5 opacity-90 group-hover:opacity-100 object-contain">
               </div>
               <span class="font-medium group-hover:text-green-300 text-sm md:text-base">Группы</span>
            </div>
         </a>
         
         <a href="{{ route('mailIndex') }}" class="group">
            <div class="flex items-center gap-3 hover:bg-gray-700/50 transition-all duration-200 rounded-lg px-4 py-3 md:py-2.5 group-active:scale-95 relative">
               <div class="p-1.5 bg-gray-700/50 group-hover:bg-red-500 rounded-lg transition-colors duration-200 flex items-center justify-center w-9 h-9">
                  <div class="relative">
                     <img src="{{ asset('img/logos/mail.png') }}" alt="" class="w-5 h-5 opacity-90 group-hover:opacity-100 object-contain">
                     @if($unreadMessagesCount > 0)
                        <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                           {{ $unreadMessagesCount < 10 ? $unreadMessagesCount : '9+' }}
                        </div>
                     @endif
                  </div>
               </div>
               <span class="font-medium group-hover:text-red-300 text-sm md:text-base">Почта</span>
            </div>
         </a>
         
         <a href="{{route('info')}}" class="group">
            <div class="flex items-center gap-3 hover:bg-gray-700/50 transition-all duration-200 rounded-lg px-4 py-3 md:py-2.5 group-active:scale-95">
               <div class="p-1.5 bg-gray-700/50 group-hover:bg-gray-500 rounded-lg transition-colors duration-200 flex items-center justify-center w-9 h-9">
                  <img src="{{asset('img/logos/info.png')}}" alt="" class="w-5 h-5 opacity-90 group-hover:opacity-100 object-contain">
               </div>
               <span class="font-medium group-hover:text-gray-300 text-sm md:text-base">Справка</span>
            </div>
         </a>
      </div>
   </div>

   <!-- Нижняя часть меню -->
   <div class="flex flex-col gap-1 px-2 mb-6">
      @if($user && $user->role === 'admin')
         <a href="{{ route('admin.index') }}" class="group">
            <div class="flex items-center gap-3 hover:bg-gray-700/50 transition-all duration-200 rounded-lg px-4 py-3 md:py-2.5 group-active:scale-95">
               <div class="p-1.5 bg-gray-700/50 group-hover:bg-indigo-500 rounded-lg transition-colors duration-200 flex items-center justify-center w-9 h-9">
                  <img src="{{ asset('img/logos/settings.png') }}" alt="" class="w-5 h-5 opacity-90 group-hover:opacity-100 object-contain">
               </div>
               <span class="font-medium group-hover:text-indigo-300 text-sm md:text-base">Управление</span>
            </div>
         </a>
      @endif

      @if($user && $user->role !== 'admin')
         <div class="px-4 py-3 rounded-lg bg-gray-700/30 mt-2">
            <div class="font-medium text-gray-300 text-xs md:text-base truncate">{{ $user->name ?? 'Гость' }} {{ $user->surname ?? '' }}</div>
         </div>
      @endif
      
      @if($user)
         <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full group">
               <div class="flex items-center gap-3 hover:bg-gray-700/50 transition-all duration-200 rounded-lg px-4 py-3 md:py-2.5 group-active:scale-95 mt-2">
                  <div class="p-1.5 bg-gray-700/50 group-hover:bg-rose-500 rounded-lg transition-colors duration-200 flex items-center justify-center w-9 h-9">
                     <img src="{{ asset('img/logos/logout.png') }}" alt="" class="w-5 h-5 opacity-90 group-hover:opacity-100 object-contain">
                  </div>
                  <span class="font-medium group-hover:text-rose-300 text-sm md:text-base">Выйти</span>
               </div>
            </button>
         </form>
      @endif
   </div>
</div>