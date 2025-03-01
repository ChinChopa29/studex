@if (session()->has('success'))
<div class="bg-green-600 flex gap-4 p-4 mb-4 rounded-2xl justify-between fixed bottom-4 left-4" id="alert-div">
   <h1 class="text-white text-base">{{session('success')}}</h1> 
</div>
@endif

<script src="{{asset('js/alert-pop-up.js')}}"></script>