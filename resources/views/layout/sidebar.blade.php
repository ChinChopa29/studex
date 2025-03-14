@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

<div class="text-white  h-screen flex flex-col justify-between">
   <div class="mt-4 flex flex-col gap-3">
      <a href="{{ $user instanceof \App\Models\Student ? route('studentCoursesIndex') : (Route::has('teacherCoursesIndex') ? route('teacherCoursesIndex') : '#') }}" class="font-bold text-xl px-4">
         STUDEX
      </a>
      <a href="#">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
            <img src="{{asset('img/logos/male.png')}}" alt="" class="w-7">
            Аккаунт
         </div>
      </a>
      <a href="{{ $user instanceof \App\Models\Student ? route('studentCoursesIndex') : (Route::has('teacherCoursesIndex') ? route('teacherCoursesIndex') : '#') }}">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
             <img src="{{ asset('img/logos/book.png') }}" alt="" class="w-7">
             Курсы
         </div>
      </a>
      <a href="#">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
            <img src="{{asset('img/logos/people.png')}}" alt="" class="w-7">
            Группы
         </div>
      </a>
      <a href="#">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
            <img src="{{asset('img/logos/calendar.png')}}" alt="" class="w-7">
            Календарь
         </div>
      </a>
      
      <a href="{{ route('mailIndex') }}">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2 relative">
               <div class="relative">
                  <img src="{{ asset('img/logos/mail.png') }}" alt="Mail Icon" class="w-7">
                  @if($unreadMessagesCount > 0)
                     <div class="absolute top-0 left-0 transform -translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                        {{ $unreadMessagesCount }}
                     </div>
                  @endif
               </div>
             Почта
         </div>
      </a>
     
     

      <a href="#">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
            <img src="{{asset('img/logos/info.png')}}" alt="" class="w-7">
            Справка
         </div>
      </a>
   </div>

   <div class="flex flex-col gap-3 mb-4">
      @if($user && $user->role === 'admin')
         <a href="{{ route('admin.index') }}">
            <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
                  <img src="{{ asset('img/logos/settings.png') }}" alt="" class="w-7">
                  Управление
            </div>
         </a>
      @endif

      @if($user && $user->role !== 'admin')
         <a href="{{ route('studentProfile', ['student' => $user->id]) }}">
            <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
               {{ $user->name ?? 'Гость' }} {{ $user->surname ?? '' }}
            </div>
         </a>
      @endif
      @if($user)
         <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 pl-5 py-2">
               <img src="{{ asset('img/logos/logout.png') }}" alt="" class="w-7">
               Выйти
            </button>
         </form>
      @endif

   </div>
</div>