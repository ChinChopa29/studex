<div class="text-white  h-screen flex flex-col justify-between">
   <div class="mt-4 flex flex-col gap-3">
      <a href="{{route('index')}}" class="font-bold text-xl px-4">STUDEX</a>
      <a href="#">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
            <img src="{{asset('img/logos/male.png')}}" alt="" class="w-7">
            Аккаунт
         </div>
      </a>
      <a href="#">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
            <img src="{{asset('img/logos/book.png')}}" alt="" class="w-7">
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
      <a href="#">
         <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
            <img src="{{asset('img/logos/mail.png')}}" alt="" class="w-7">
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
      @php
         $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
      @endphp

      @if($user && $user->role === 'admin')
         <a href="{{ route('admin.index') }}">
            <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
                  <img src="{{ asset('img/logos/settings.png') }}" alt="" class="w-7">
                  Управление
            </div>
         </a>
      @endif

      @if($user && $user->role !== 'admin')
         <a href="{{ route('studentProfile', ['student' => Auth::user()->id]) }}">
            <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
                  {{Auth::user()->name}} {{Auth::user()->surname}}
            </div>
         </a>
      @endif
      @if($user)
         <a href="{{ route('logout') }}">
            <div class="flex items-center gap-2 hover:bg-slate-700 transition-all duration-200 w-full pl-5 py-2">
                  <img src="{{ asset('img/logos/logout.png') }}" alt="" class="w-7">
                  Выйти
            </div>
         </a>
      @endif

   </div>
</div>