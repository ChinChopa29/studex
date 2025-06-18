<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\TestTask;
use App\Models\TestQuestion;
use App\Models\TestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestTaskController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'milestone_id' => 'nullable|exists:milestones,id',
            'from' => 'required|date',
            'deadline' => 'required|date|after:from',
            'shuffle_questions' => 'sometimes|accepted',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.shuffle_answers' => 'sometimes|accepted',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*' => 'required|string',
            'questions.*.correct_answer' => 'required|integer|min:0'
        ]);

        $validated['shuffle_questions'] = $request->has('shuffle_questions');

        try {
            DB::beginTransaction();

            $testTask = TestTask::create([
                'course_id' => $course->id,
                'milestone_id' => $validated['milestone_id'],
                'name' => $validated['name'],
                'from' => $validated['from'],
                'deadline' => $validated['deadline'],
                'shuffle_questions' => $validated['shuffle_questions']
            ]);

            foreach ($validated['questions'] as $questionData) {
                $question = TestQuestion::create([
                    'test_task_id' => $testTask->id,
                    'text' => $questionData['text'],
                    'shuffle_answers' => isset($questionData['shuffle_answers'])
                ]);

                foreach ($questionData['answers'] as $index => $answerText) {
                    TestAnswer::create([
                        'test_question_id' => $question->id,
                        'text' => $answerText,
                        'is_correct' => $index == $questionData['correct_answer']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('CourseTasks', ['course' => $course->id])
                ->with('success', 'Тест успешно создан!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Произошла ошибка при создании теста: ' . $e->getMessage());
        }
    }
}