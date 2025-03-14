@extends('layout.layout')

@section('title') 
   Написать сообщение
@endsection

@section('content')

@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@if($user) 
   <div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">  
      <div class="flex items-center gap-4 mb-6">
         <a href="{{ route('mailIndex') }}" class="p-2 border-2 border-transparent rounded-full hover:border-gray-400 transition">
            <i class="fa fa-arrow-left text-2xl hover:text-gray-400"></i>
         </a>
         <h2 class="text-2xl font-bold text-gray-200">Написать сообщение</h2>
      </div>

      <form action="{{ route('mailStore') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
         @csrf
         <label class="block text-gray-200">Кому:</label>
         <select id="recipient_type" name="receiver_type" class="w-full p-2 rounded bg-gray-700 text-white">
             <option value="student">Студенту</option>
             <option value="teacher">Преподавателю</option>
         </select>
     
         <div id="recipient_search" class="relative">
            <input type="text" id="recipient_id" name="recipient_fio" class="w-full p-2 rounded bg-gray-700 text-white" placeholder="Начните вводить имя">
            <input type="hidden" id="receive_id" name="receiver_id"> <!-- Теперь скрытый инпут для ID -->
            <div id="search_results" class="absolute w-full bg-gray-800 rounded mt-1 shadow-lg z-10"></div>
        </div>
        
     
         <label class="block text-gray-200">Сообщение:</label>
         <textarea name="message" class="w-full p-2 rounded bg-gray-700 text-white" rows="4"></textarea>
     
         <label class="block text-gray-200">Прикрепить файлы:</label>
         <input type="file" name="files[]" multiple class="w-full text-white bg-gray-700 p-2 rounded">
     
         <button type="submit" class="w-full p-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Отправить</button>
     </form>
     
   </div>
@endif

<script>
   function toggleRecipientSearch() {
      let type = document.getElementById('recipient_type').value;
      let searchField = document.getElementById('recipient_search');
      let searchInput = document.getElementById('recipient_id');

      if (type === 'admin') {
         searchField.style.display = 'none';
         searchInput.value = 'admin@studex.com';
      } else {
         searchField.style.display = 'block';
         searchInput.value = '';
      }
   }

   document.getElementById('recipient_id').addEventListener('input', function() {
    let query = this.value.trim();
    let type = document.getElementById('recipient_type').value;

    if (query.length > 0) {
        fetch(`/search/${type}?query=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Ошибка HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                let results = document.getElementById('search_results');
                results.innerHTML = '';

                if (data.length === 0) {
                    results.innerHTML = '<div class="p-2 text-gray-400">Ничего не найдено</div>';
                    return;
                }

                data.forEach(user => {
                    let div = document.createElement('div');
                    div.classList.add('p-2', 'hover:bg-gray-700', 'cursor-pointer');
                    div.textContent = user.full_name;
                    div.onclick = function() {
                        document.getElementById('recipient_id').value = user.full_name; 
                        document.getElementById('receive_id').value = user.id; 
                        results.innerHTML = ''; 
                    };
                    results.appendChild(div);
                });
            })
            .catch(error => console.error('Ошибка поиска:', error));
    }
   });
   toggleRecipientSearch(); 
</script>
@include('include.success-message')
@endsection
