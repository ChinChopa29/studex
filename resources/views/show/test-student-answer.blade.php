@extends('layout.layout')
@section('title') 
Результат теста студента - {{ $student->fullName }}
@endsection

@section('content')
<div class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Хлебные крошки -->
        <div class="flex flex-col space-y-4 mb-8">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('CoursesIndex') }}" class="text-blue-400 hover:text-blue-300">Курсы</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseShow', $course->id) }}" class="text-blue-400 hover:text-blue-300">{{ $course->name }}</a>
                <span class="text-gray-500">/</span>
                <a href="{{ route('CourseTestTask', ['course' => $course->id, 'testTask' => $testTask->id]) }}" class="text-blue-400 hover:text-blue-300">{{ $testTask->name }}</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-400">Результат: {{ $student->surname }} {{ $student->name }}</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('CourseTestTask', ['course' => $course->id, 'testTask' => $testTask->id]) }}" class="p-2 rounded-full bg-gray-800 hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl font-bold">Результат теста</h1>
                </div>
            </div>
        </div>

        <!-- Информация о студенте и тесте -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-700 p-4 rounded-lg">
                    <div class="flex items-center space-x-2 text-blue-400">
                        <i class="fas fa-user-graduate"></i>
                        <span class="font-medium">Студент:</span>
                    </div>
                    <p class="mt-1 text-lg font-semibold">{{ $student->surname }} {{ $student->name }} {{ $student->lastname }}</p>
                    <p class="text-gray-400 text-sm">{{ $student->email }}</p>
                </div>
                
                <div class="bg-gray-700 p-4 rounded-lg">
                    <div class="flex items-center space-x-2 text-green-400">
                        <i class="fas fa-check-circle"></i>
                        <span class="font-medium">Результат:</span>
                    </div>
                    <p class="mt-1 text-lg font-semibold">
                        <span class="{{ $testResult->score >= 60 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $testResult->score }}/100
                        </span>
                        ({{ $testResult->correct_answers }} из {{ $testResult->total_questions }})
                    </p>
                    <p class="text-gray-400 text-sm">
                        {{ \Carbon\Carbon::parse($testResult->created_at)->translatedFormat('d F Y H:i') }}
                    </p>
                </div>
                
                <div class="bg-gray-700 p-4 rounded-lg">
                    <div class="flex items-center space-x-2 text-yellow-400">
                        <i class="fas fa-tasks"></i>
                        <span class="font-medium">Тест:</span>
                    </div>
                    <p class="mt-1 text-lg font-semibold">{{ $testTask->name }}</p>
                    <p class="text-gray-400 text-sm">
                        {{ \Carbon\Carbon::parse($testTask->deadline)->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Результаты по вопросам -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-6 flex items-center">
                <i class="fas fa-question-circle text-blue-400 mr-2"></i>
                Ответы на вопросы
            </h2>
            
            <div class="space-y-6">
                @foreach($questions as $question)
                    @php
                        $selectedAnswerId = $selectedAnswers[$question->id] ?? null;
                        $isCorrect = false;
                        $correctAnswer = $question->answers->firstWhere('is_correct', true);
                        
                        if ($selectedAnswerId) {
                            $selectedAnswer = $question->answers->firstWhere('id', $selectedAnswerId);
                            $isCorrect = $selectedAnswer ? $selectedAnswer->is_correct : false;
                        }
                    @endphp
                    
                    <div class="bg-gray-700 p-5 rounded-lg question-block border-l-4 {{ $isCorrect ? 'border-green-500' : 'border-red-500' }}">
                        <div class="flex items-start mb-4">
                            <span class="bg-gray-600 text-white text-sm font-semibold mr-3 px-2.5 py-0.5 rounded-full">{{ $loop->iteration }}</span>
                            <h4 class="text-lg font-medium">{{ $question->text }}</h4>
                        </div>
                        
                        <div class="space-y-3 answers-container ml-10">
                            @foreach($question->answers as $answer)
                                <div class="flex items-center p-3 rounded-lg 
                                    @if($answer->is_correct) bg-green-900 bg-opacity-30 border border-green-700
                                    @elseif($answer->id == $selectedAnswerId) bg-red-900 bg-opacity-30 border border-red-700
                                    @else bg-gray-600 @endif">
                                    <div class="flex items-center">
                                        <input type="radio" 
                                               class="form-radio h-4 w-4 
                                                   @if($answer->is_correct) text-green-500
                                                   @elseif($answer->id == $selectedAnswerId) text-red-500
                                                   @else text-gray-400 @endif"
                                               disabled
                                               @if($answer->id == $selectedAnswerId) checked @endif>
                                        <label class="ml-2 @if($answer->is_correct) text-green-400 @endif">
                                            {{ $answer->text }}
                                        </label>
                                    </div>
                                    
                                    @if($answer->id == $selectedAnswerId)
                                        <span class="ml-auto text-sm font-medium">
                                            @if($isCorrect)
                                                <span class="text-green-400">✓ Верный ответ</span>
                                            @else
                                                <span class="text-red-400">✗ Неверный ответ</span>
                                            @endif
                                        </span>
                                    @elseif($answer->is_correct && !$selectedAnswerId)
                                        <span class="ml-auto text-sm font-medium text-green-400">
                                            Правильный ответ (не выбран)
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        @if(!$selectedAnswerId)
                            <div class="mt-3 ml-10 text-red-400 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span>Студент не ответил на этот вопрос</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <!-- Кнопка возврата -->
            <div class="mt-8 flex justify-end">
                <a href="{{ route('CourseTestTask', ['course' => $course->id, 'testTask' => $testTask->id]) }}" 
                   class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                    Назад к тесту
                </a>
            </div>
        </div>
    </div>
</div>
@endsection