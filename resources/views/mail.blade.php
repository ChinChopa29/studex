@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
Почта
@endsection

@section('content')

@if($user) 
@section('page-name', 'Входящие') 
@include('layout.mail-menu')

@push('return-to')
    {{ route('mailIndex') }}
@endpush
@include('layout.mail-search', ['mail_type' => 'inbox'])

      <form action="{{ route('mailBulkAction') }}" method="POST" id="bulkActionForm">
         @csrf
         <div class="divide-y divide-gray-600">
            @if($messages->count() > 1)
                <div class="flex items-center gap-6 mb-4 pl-4">
                    <input type="checkbox" id="selectAll" class="w-5 h-5 border border-gray-400 rounded-lg">
                    <label for="selectAll" class="text-gray-300 cursor-pointer">Выбрать все</label>
                </div>
            @endif
            @forelse ($messages as $message)
             
                 <div class="relative flex items-center px-4 py-6 hover:bg-slate-700 transition-all gap-4 w-full 
                     {{ $message->status === 0 ? 'bg-slate-900 font-bold' : 'bg-slate-800' }}">
                     
                     <input type="checkbox" name="messages[]" value="{{ $message->id }}" 
                            class="messageCheckbox w-5 h-5 border border-gray-400 rounded-lg mr-4 flex-shrink-0 relative z-10">
     
                     <a href="{{ route('messageShow', ['message' => $message->id]) }}" 
                        class="absolute inset-0 flex items-center gap-4 px-4 py-6 text-white no-underline">
                         
                         <div class="w-1/6 truncate text-gray-300 pl-12">
                             @if($message->sender_id === Auth::id() && $message->sender_type === get_class(Auth::user()))
                                 Вы
                             @else
                                 {{ $message->sender->surname }} {{ $message->sender->name }}
                             @endif
                         </div>
                         <div class="flex-1 truncate text-gray-400">
                             {{ $message->message }}
                         </div>
                         <div class="w-1/5 text-right text-gray-500 text-sm">
                             {{ $message->created_at->format('d.m.Y H:i') }}
                         </div>
                     </a>
                 </div>
             @empty
                 <h1 class="text-center text-gray-500">📭 У вас пока нет сообщений</h1>
             @endforelse
         </div>
         
         <div class="flex gap-4 mt-4" id="bulkActions" style="display: none;">
             <button type="submit" name="action" value="read" 
                     class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200">
                 Отметить как прочитанное
             </button>
             <button type="submit" name="action" value="favorite" 
                     class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-all duration-200">
                 В избранное
             </button>
             <button type="submit" name="action" value="delete" 
                     class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200">
                 Удалить
             </button>
         </div>
     </form>
    <div class="mt-6">
        {{ $messages->links() }}
    </div>
</div>
@endif
<script>
   document.addEventListener("DOMContentLoaded", function() {
       const checkboxes = document.querySelectorAll(".messageCheckbox");
       const bulkActions = document.getElementById("bulkActions");

       function toggleBulkActions() {
           const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
           bulkActions.style.display = anyChecked ? "flex" : "none";
       }

       checkboxes.forEach(checkbox => {
           checkbox.addEventListener("change", toggleBulkActions);
       });
   });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkboxes = document.querySelectorAll(".messageCheckbox");
        const bulkActions = document.getElementById("bulkActions");
        const selectAllCheckbox = document.getElementById("selectAll");
 
        function toggleBulkActions() {
            const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
            bulkActions.style.display = anyChecked ? "flex" : "none";
        }
 
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", toggleBulkActions);
        });
 
        selectAllCheckbox.addEventListener("change", function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            toggleBulkActions(); 
        });
    });
</script>
@include('include.success-message')
@endsection
