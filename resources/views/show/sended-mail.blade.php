@extends('layout.layout')

@section('title') 
Ваше сообщение
@endsection

@section('content')

@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@if($user) 
   <div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">  
      <div class="flex items-center gap-4 mb-6">
         <a href="{{ route('mailSended') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
            <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
         </a>
         <h2 class="text-2xl font-bold text-gray-200">Сообщение</h2>
      </div>
      
      <div class="mb-4">
         <h3 class="text-lg font-semibold text-gray-300">
            Отправитель: {{ $message->sender->surname }} {{ $message->sender->name }} {{ $message->sender->lastname }}
         </h3>
         <p class="text-gray-400 text-sm">
            Email: {{ $message->sender->email ?? 'Не указан' }}
         </p>

         <h3 class="text-lg font-semibold text-gray-300">
            Получатель: {{ $message->receiver->surname }} {{ $message->receiver->name }} {{ $message->receiver->lastname }}
         </h3>
         <p class="text-gray-400 text-sm">
            Email: {{ $message->receiver->email ?? 'Не указан' }}
         </p>
         <p class="text-gray-500 text-sm">
            Отправлено: {{ $message->created_at->format('d.m.Y H:i') }}
         </p>
      </div>

      <div class="bg-slate-700 p-6 rounded-lg text-gray-300 shadow-md">
         @if($message->type === 'invite')
            <div class="flex flex-col">
               {{ $message->message }}
               
            </div>
         @else
            {{ $message->message }}
         @endif
      </div>
      @if($message->files->count() > 0)
      <div class="mt-4">
          <h3 class="text-lg font-semibold text-gray-300">Прикрепленные файлы:</h3>
          <ul class="list-disc list-inside">
              @foreach($message->files as $file)
                  <li>
                      <a href="{{ asset('storage/' . $file->file_path) }}" class="text-blue-400 hover:underline" download>
                          {{ basename($file->file_path) }}
                      </a>
                  </li>
              @endforeach
          </ul>
      </div>
      @endif

   </div>
@endif
@include('include.success-message')
@endsection
