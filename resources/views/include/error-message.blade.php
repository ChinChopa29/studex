@if (session()->has('error'))
<div class="bg-red-500 flex gap-4 p-4 mb-4 rounded-2xl justify-between fixed bottom-4 left-4" id="alert-div">
   <h1 class="text-white text-base">{{ session('error') }}</h1> 
</div>
@endif

@if ($errors->any())
<div class="bg-red-500 flex flex-col gap-2 p-4 mb-4 rounded-2xl fixed bottom-4 left-4" id="alert-div">
    <ul class="list-disc list-inside text-white text-base">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<script src="{{ asset('js/alert-pop-up.js') }}"></script>
