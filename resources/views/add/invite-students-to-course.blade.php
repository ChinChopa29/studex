@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
{{$course->name}}
@endsection


@section('content')
@if($user)
   <div class="bg-slate-800 text-white m-4 p-6 rounded-2xl shadow-lg">
      @include('show.course-layout')

      <div class="mt-6 p-4 rounded-xl shadow-inner">
         <form action="{{ route('teacherCourseInvite', ['course' => $course->id]) }}" method="post">
            @csrf
            <div class="flex flex-col gap-4">
               <label for="group" class="text-lg font-medium">Доступные группы:</label>
               <select name="groupId" id="groupSelect" class="w-full md:w-1/3 bg-gray-200 text-black border-2 border-gray-300 rounded-lg py-2 px-4">
                  <option value="">Выберите группу</option>
                  @forelse ($groups as $group)
                      @if($course->educationPrograms->contains('id', $group->education_program_id))
                          <option value="{{ $group->id }}">{{ $group->name }}</option>
                      @endif
                  @empty
                      <option disabled>Нет доступных групп</option>
                  @endforelse
               </select>
            </div>

            <div id="studentsList" class="mt-4">
               <p class="text-gray-500">Выберите группу, чтобы увидеть студентов.</p>
            </div>

            <button class="w-full md:w-1/3 bg-green-600 hover:bg-green-700 transition-all duration-200 rounded-lg mt-4 py-3 px-6 text-lg font-medium shadow-md flex items-center justify-center gap-2">
               Пригласить группу <i class="fas fa-plus text-lg"></i> 
            </button>
         </form>
      </div>

   </div>
@endif


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
    $('#groupSelect').change(function () {
        let groupId = $(this).val();
        if (groupId) {
            $.ajax({
                url: "{{ route('getGroupStudents') }}",
                type: "GET",
                data: { 
                    group_id: groupId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    console.log(response); 
                    $('#studentsList').html(response);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText); 
                    $('#studentsList').html('<p class="text-red-500">Ошибка загрузки студентов</p>');
                }
            });
        } else {
            $('#studentsList').html('<p class="text-gray-500">Выберите группу, чтобы увидеть студентов.</p>');
        }
   });
   });
</script>
@include('include.success-message')
@endsection