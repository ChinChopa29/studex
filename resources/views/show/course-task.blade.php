@php
   $user = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
@endphp

@extends('layout.layout')
@section('title') 
{{$task->name}}
@endsection

@section('content')
@if($user)
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseShow', ['course' => $course->id, 'task' => $task->id]) }}" class="text-blue-400 hover:text-blue-300">{{ $course->name }}</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseTasks', ['course' => $course->id]) }}" class="text-blue-400 hover:text-blue-300">Задания</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">{{ $task->name }}</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseTasks', ['course' => $course->id])}}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">{{ $task->name }}</h1>
                </div>
                
                @if(Auth::guard('teacher')->check() && (!isset($editing) || !$editing))
                <div class="flex space-x-3">
                    <a href="{{ route('CourseTaskEdit', ['course' => $course->id, 'task' => $task->id]) }}" 
                       class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i> Редактировать
                    </a>
                    <form action="{{route('CourseTaskDelete', ['course' => $course->id, 'task' => $task->id])}}" 
                          onsubmit="return confirm('Вы уверены, что хотите удалить эту задачу?');" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-trash mr-2"></i> Удалить
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Основной контент -->
        <div class="grid grid-cols-1 gap-8">
            <!-- Левая колонка - информация о задании -->
            <div class="space-y-6">
                @if(!isset($editing) || !$editing)
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <div class="prose prose-invert max-w-none">
                        <p class="text-gray-300 mb-6">{{ $task->description }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center space-x-2 text-blue-400">
                                    <i class="far fa-calendar-alt"></i>
                                    <span class="font-medium">Дата начала:</span>
                                </div>
                                <p class="mt-1 text-lg font-semibold">
                                    {{ \Carbon\Carbon::parse($task->from)->translatedFormat('j F Y года') }}
                                </p>
                            </div>
                            
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center space-x-2 text-red-400">
                                    <i class="far fa-clock"></i>
                                    <span class="font-medium">Дедлайн:</span>
                                </div>
                                <p class="mt-1 text-lg font-semibold">
                                    {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('j F Y года') }}
                                </p>
                            </div>
                        </div>
                        
                        @if($task->teacherFiles->isNotEmpty())
                        <div class="mt-6">
                            <h3 class="text-xl font-semibold mb-3 flex items-center">
                                <i class="far fa-file-alt mr-2 text-blue-400"></i>
                                Материалы задания
                            </h3>
                            <div class="space-y-2">
                                @foreach($task->teacherFiles as $file)
                                <div class="flex items-center justify-between bg-gray-700 p-3 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                    <div class="flex items-center space-x-3">
                                        <i class="far fa-file text-blue-400"></i>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" 
                                           class="text-blue-400 hover:underline truncate max-w-xs"
                                           download="{{ $file->original_name }}">
                                            {{ $file->original_name }}
                                        </a>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ round(filesize(public_path('storage/' . $file->file_path)) / 1024) }} KB</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Форма редактирования -->
                @if(isset($editing) && $editing)
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h2 class="text-2xl font-bold mb-6">Редактирование задания</h2>
                    <form action="{{ route('CourseTaskUpdate', ['course' => $course->id, 'task' => $task->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="name" class="block mb-2 font-medium">Название задания</label>
                            <input type="text" id="name" name="name" value="{{ $task->name }}" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="description" class="block mb-2 font-medium">Описание</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $task->description) }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="from" class="block mb-2 font-medium">Дата начала</label>
                                <input type="date" id="from" name="from" value="{{ $task->from ? $task->from->format('Y-m-d') : '' }}" 
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="deadline" class="block mb-2 font-medium">Дедлайн</label>
                                <input type="date" id="deadline" name="deadline" value="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}" 
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <input type="hidden" name="deleted_files" id="deletedFilesInput">

                        @if($task->teacherFiles->isNotEmpty())
                        <div>
                            <h3 class="text-xl font-semibold mb-4">Прикрепленные файлы</h3>
                            <div class="space-y-3">
                                @foreach($task->teacherFiles as $file)
                                <div id="file-{{ $file->id }}" class="flex items-center justify-between bg-gray-700 p-3 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <i class="far fa-file text-blue-400"></i>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" 
                                           class="text-blue-400 hover:underline truncate max-w-xs"
                                           download="{{ $file->original_name }}">
                                            {{ $file->original_name }}
                                        </a>
                                    </div>
                                    <button type="button" class="text-red-500 hover:text-red-400 delete-file" data-file-id="{{ $file->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <input type="hidden" name="existing_files[]" value="{{ $file->id }}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div>
                            <label class="block mb-2 font-medium">Добавить файлы</label>
                            <div class="flex items-center space-x-4">
                                <label for="fileInput" class="cursor-pointer bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg border border-gray-600 transition-colors duration-200 flex items-center space-x-2">
                                    <i class="fas fa-paperclip"></i>
                                    <span>Выберите файлы</span>
                                </label>
                                <input type="file" id="fileInput" name="new_files[]" multiple class="hidden">
                            </div>
                            <ul id="newFileList" class="mt-3 space-y-2"></ul>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4">
                            <a href="{{ route('CourseTask', ['course' => $course->id, 'task' => $task->id]) }}" 
                               class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                                Отмена
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                <i class="fas fa-save"></i>
                                <span>Сохранить изменения</span>
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Блок для студентов -->
                @if(Auth::guard('student')->check() && $task->from <= now() && $task->deadline > now())
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    @php
                        $studentFiles = $task->studentFiles->where('student_id', Auth::id());
                        $grade = $task->grades->where('student_id', Auth::id())->first();
                    @endphp

                    <!-- Если есть оценка - показываем её в любом случае -->
                    @if($grade)
                    <div class="bg-gray-700 p-4 rounded-lg mb-6">
                        <h4 class="font-medium text-lg mb-2">Оценка</h4>
                        <div class="flex items-center space-x-3">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-4">
                                    <span class="text-2xl font-bold {{ $grade->grade >= 60 ? 'text-green-400' : 'text-yellow-400' }}">
                                        {{ $grade->grade }}/100
                                    </span>
                                    @if($grade->grade >= 90)
                                    <span class="px-2 py-1 bg-green-900 text-green-300 text-xs rounded-full">Отлично</span>
                                    @elseif($grade->grade >= 75)
                                    <span class="px-2 py-1 bg-blue-900 text-blue-300 text-xs rounded-full">Хорошо</span>
                                    @elseif($grade->grade >= 60)
                                    <span class="px-2 py-1 bg-yellow-900 text-yellow-300 text-xs rounded-full">Удовлетворительно</span>
                                    @else
                                    <span class="px-2 py-1 bg-red-900 text-red-300 text-xs rounded-full">Неудовлетворительно</span>
                                    @endif
                                </div>
                                <div>
                                    @if(isset($grade->comment))
                                        <span class="text-sm text-gray-400">
                                            Комментарий: {{ $grade->comment }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    @endif

                    <!-- Форма загрузки файлов показывается, если нет файлов И нет комментариев -->
                    @if($studentFiles->count() == 0 && !$grade && !$comment)
                    <form action="{{route('CourseTaskUpload', ['course' => $course->id, 'task' => $task->id])}}" method="post" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <h3 class="text-xl font-semibold mb-2">Отправить решение</h3>
                        <p class="text-gray-400 mb-4">Загрузите файлы с вашим решением задания</p>
                        
                        <div class="flex items-center space-x-4">
                            <label for="fileInput" class="cursor-pointer bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg border border-gray-600 transition-colors duration-200 flex items-center space-x-2">
                                <i class="fas fa-paperclip"></i>
                                <span>Выберите файлы</span>
                            </label>
                            <input type="file" id="fileInput" name="files[]" multiple class="hidden">
                        </div>
                        
                        <ul id="fileList" class="mt-3 space-y-2"></ul>

                        <div class="mt-6">
                            <label for="comment" class="block mb-2 font-medium">Комментарий</label>
                            <textarea id="comment" name="comment" rows="3" 
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                    placeholder="Введите комментарий">{{old('comment')}}</textarea>
                        </div>
                        
                        <button type="submit" class="mt-4 px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Отправить решение</span>
                        </button>
                    </form>
                    @elseif($studentFiles->count() > 0 || $comment)
                    <div>
                        <h3 class="text-xl font-semibold mb-4">Ваше решение</h3>
                        
                        <!-- Выводим файлы, если они есть -->
                        @if($studentFiles->count() > 0)
                            <div class="space-y-3 mb-6">
                                @foreach($studentFiles as $file)
                                <div class="flex items-center justify-between bg-gray-700 p-3 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <i class="far fa-file text-blue-400"></i>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" 
                                        class="text-blue-400 hover:underline truncate max-w-xs"
                                        download="{{ $file->original_name }}">
                                            {{ $file->original_name }}
                                        </a>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ round(filesize(public_path('storage/' . $file->file_path)) / 1024) }} KB</span>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Если есть комментарий, выводим его -->
                        @if($comment)
                            <div class="mt-4">
                                <h4 class="font-medium text-gray-400">Комментарий:</h4>
                                <p class="text-gray-200">{{ $comment->comment }}</p>
                            </div>
                        @endif
                        
                        @unless($grade)
                        <div class="bg-gray-700 p-4 rounded-lg mt-4">
                            <div class="flex items-center space-x-2 text-yellow-400">
                                <i class="fas fa-hourglass-half"></i>
                                <span>Задание еще не оценено</span>
                            </div>
                        </div>
                        @endunless
                    </div>
                    @endif

                </div>
                @endif
                </div>
            </div>

            @if(Auth::guard('teacher')->check() && (!isset($editing) || !$editing))
            <div class="w-full space-y-6 mt-6">
                
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-blue-400"></i>
                        Статистика
                    </h3>
                    @php
                        $totalStudents = $groups->flatMap(fn($group) => $group->students)->unique('id')->count();

                        $submittedStudents = collect();

                        if ($task->studentFiles) {
                            $submittedStudents = $submittedStudents->merge(
                                $task->studentFiles->pluck('student_id')
                            );
                        }
                        if ($task->comments) {
                            $commentingStudents = $task->comments
                                ->whereNotNull('student_id')
                                ->pluck('student_id');

                            $submittedStudents = $submittedStudents->merge($commentingStudents);
                        }

                        $submittedCount = $submittedStudents->unique()->count();

                        $gradedCount = $task->grades ? $task->grades->count() : 0;

                    @endphp
                    <div class="space-y-4">
                        <div>
                        <div class="flex justify-between mb-1">
                            <span class="font-medium">Сдали работу</span>
                            <span class="font-medium">{{ $submittedCount }}/{{ $totalStudents }}</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $totalStudents > 0 ? ($submittedCount / $totalStudents) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                        
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="font-medium">Проверено работ</span>
                                <span class="font-medium">{{ $gradedCount }}/{{ $totalStudents }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2.5">
                                <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $gradedCount > 0 ? ($gradedCount/$totalStudents)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-search mr-2 text-blue-400"></i>
                        Поиск и фильтры
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Поиск по студентам -->
                        <div>
                            <label for="studentSearch" class="block mb-2 text-sm font-medium">Поиск студента</label>
                            <input type="text" id="studentSearch" placeholder="ФИО студента..." 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Фильтр по группе -->
                        <div>
                            <label for="groupFilter" class="block mb-2 text-sm font-medium">Группа</label>
                            <select id="groupFilter" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Все группы</option>
                                @foreach($groups->filter(fn($group) => empty($group->subgroup)) as $group)
                                    <option value="group-{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Фильтр по статусу -->
                        <div>
                            <label for="statusFilter" class="block mb-2 text-sm font-medium">Статус задания</label>
                            <select id="statusFilter" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Все статусы</option>
                                <option value="checked">Проверено</option>
                                <option value="waiting">Ожидание проверки</option>
                                <option value="not_submitted">Не сдано</option>
                            </select>           
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-users mr-2 text-yellow-400"></i>
                        Группы и оценки
                    </h3>
                    
                    <div class="space-y-6">

                        @foreach($groups->filter(fn($group) => empty($group->subgroup)) as $group)
                            <div data-group-container="{{ $group->id }}" class="group-container">
                                <h4 class="font-semibold text-lg mb-3 text-yellow-400">{{ $group->name }}</h4>
                                
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-gray-700 text-left">
                                                <th class="p-3">Студент</th>
                                                <th class="p-3">Статус</th>
                                                <th class="p-3">Оценка</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-700">
                                            @foreach($group->students as $student)
                                                @php
                                                    $submission = $task->studentFiles?->where('student_id', $student->id)?->first();
                                                    $commentSubmission = $task->comments?->where('student_id', $student->id)?->first(); 
                                                    $grade = $task->grades?->where('student_id', $student->id)?->first();

                                                    if ($grade) {
                                                        $status = 'checked';
                                                        $statusText = '✅ Проверено';
                                                        $statusColor = 'text-green-400';
                                                        $score = $grade->grade !== null ? $grade->grade . '/100' : '—';
                                                    } elseif ($submission || $commentSubmission) {
                                                        $status = 'waiting';
                                                        $statusText = '⏳ Ожидание';
                                                        $statusColor = 'text-yellow-400';
                                                        $score = '—';
                                                    } else {
                                                        $status = 'not_submitted';
                                                        $statusText = '❌ Не сдано';
                                                        $statusColor = 'text-red-400';
                                                        $score = '—';
                                                    }

                                                    $studentUrl = route('CourseTaskShowStudent', [
                                                        'course' => $course->id,
                                                        'task' => $task->id,
                                                        'student' => $student->id
                                                    ]);
                                                @endphp

                                                <tr class="hover:bg-gray-700 cursor-pointer student-row" 
                                                    onclick="window.location='{{ $studentUrl }}'"
                                                    data-status="{{ $status }}">
                                                    <td class="p-3">{{ $student->surname }} {{ $student->name }}</td>
                                                    <td class="p-3 font-semibold {{ $statusColor }}">{{ $statusText }}</td>
                                                    <td class="p-3">{{ $score }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

@if(isset($editing) && $editing)
<script src="{{asset('js/add-files.js')}}"></script>
<script src="{{asset('js/delete-file.js')}}"></script>
@endif

@if(Auth::guard('student')->check())
<script src="{{asset('js/student-upload.js')}}"></script>
@endif

<script src="{{asset('js/search-student-tasks.js')}}"></script>
@include('include.success-message')
@include('include.error-message') 
@endsection