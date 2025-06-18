<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Message;
use App\Models\Milestone;
use App\Models\Student;
use App\Models\StudentTaskFile;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskGrade;
use App\Models\TeacherTaskFile;
use App\Models\TestResult;
use App\Models\TestTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request, Course $course) 
    {
        $milestones = Milestone::where('course_id', $course->id)->get();
        $selectedMilestone = $request->input('milestone', 'all');
        
        $query = $course->tasks()->with('milestone');
    
        if ($selectedMilestone !== 'all') {
            $query->where('milestone_id', $selectedMilestone);
        }
    
        $tasks = $query->orderBy('from')->get();
    
        $testTasksQuery = $course->testTasks()->with('milestone');
        
        if ($selectedMilestone !== 'all') {
            $testTasksQuery->where('milestone_id', $selectedMilestone);
        }
    
        $testTasks = $testTasksQuery->orderBy('from')->get();
    
        return view('show.course-tasks', compact(
            'course',
            'tasks',
            'milestones',
            'selectedMilestone',
            'testTasks' 
        ));
    }
    
    public function create(Course $course) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) 
        {
            abort(403);
        }
        $milestones = Milestone::where('course_id', $course->id)->get();
        return view('add.create-task', compact('course', 'milestones'));
    }


    public function store(Request $request, Course $course) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }

        try {
            $validatedData = $request->validate(
            [
                'name' => 'required',
                'description' => 'nullable',
                'from' => 'nullable|date',
                'deadline' => 'nullable|date|after_or_equal:from',
                'milestone_id' => 'nullable|exists:milestones,id',
                'files.*' => 'nullable|file',
            ], 
            [
                'name.required' => 'Название задания обязательно.',
                'from.date' => 'Дата начала должна быть корректной.',
                'deadline.date' => 'Дата дедлайна должна быть корректной.',
                'deadline.after_or_equal' => 'Дата дедлайна должна быть после даты начала.',
                'milestone_id.exists' => 'Выбранный рубежный контроль не существует.',
                'files.*.file' => 'Файл должен быть корректным.',
            ]);

            if ($request->milestone_id) {
                $milestone = Milestone::where('id', $request->milestone_id)
                    ->where('course_id', $course->id)
                    ->first();

                if (!$milestone) {
                    return back()->withErrors(['milestone_id' => 'Выбранный рубежный контроль не принадлежит этому курсу.'])->withInput();
                }

                if ($request->from && ($request->from < $milestone->from || $request->from > $milestone->deadline)) {
                    return back()->withErrors(['from' => 'Дата начала задания должна быть в пределах рубежного контроля.'])->withInput();
                }

                if ($request->deadline && ($request->deadline < $milestone->from || $request->deadline > $milestone->deadline)) {
                    return back()->withErrors(['deadline' => 'Дата дедлайна должна быть в пределах рубежного контроля.'])->withInput();
                }
            }

            $task = Task::create([
                'course_id' => $course->id,
                'milestone_id' => $validatedData['milestone_id'], 
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'from' => $validatedData['from'],
                'deadline' => $validatedData['deadline'],
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files', []) as $file) {
                    $path = $file->store('task_files', 'public');
                    TeacherTaskFile::create([
                        'task_id' => $task->id,
                        'teacher_id' => Auth::id(),
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            return back()->with('success', 'Задание успешно добавлено.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при добавлении задания.')->withInput();
        }
    }

    public function show(Course $course, Task $task) 
    {
        $task->load(['teacherFiles', 'studentFiles', 'grades', 'comments']); 

        $comment = null;
        if (auth()->guard('student')->check()) {
            $comment = TaskComment::where('task_id', $task->id)
                        ->where('student_id', auth()->guard('student')->id())
                        ->first();
        }

        $groups = Group::whereHas('students', function ($query) use ($course) {
                $query->whereHas('courses', function ($q) use ($course) {
                    $q->where('course_id', $course->id)
                    ->where('status', 'accepted');
                });
            })
            ->with(['students' => function ($query) use ($course) {
                $query->whereHas('courses', function ($q) use ($course) {
                    $q->where('course_id', $course->id)
                    ->where('status', 'accepted');
                })
                ->with(['courses' => function ($q) use ($course) {
                    $q->where('course_id', $course->id)
                    ->select('student_id', 'status');
                }]);
            }])
            ->get();

        $gradeValue = null;
        if (auth()->guard('student')->check()) {
            $grade = $task->grades->where('student_id', auth()->guard('student')->id())->first();
            $gradeValue = $grade ? $grade->grade : null;
        }
    
        return view('show.course-task', compact('task', 'course', 'groups', 'comment', 'gradeValue'));
    }

    public function showTest(Course $course, TestTask $testTask) 
    {
        $testTask->load(['questions.answers', 'testResults']);

        $testResult = null;
        if (auth()->guard('student')->check()) {
            $testResult = TestResult::where('test_task_id', $testTask->id)
                          ->where('student_id', auth()->guard('student')->id())
                          ->first();
        }

        $groups = Group::whereHas('students', function ($query) use ($course) {
                $query->whereHas('courses', function ($q) use ($course) {
                    $q->where('course_id', $course->id)
                      ->where('status', 'accepted');
                });
            })
            ->with(['students' => function ($query) use ($course) {
                $query->whereHas('courses', function ($q) use ($course) {
                    $q->where('course_id', $course->id)
                      ->where('status', 'accepted');
                })
                ->with(['courses' => function ($q) use ($course) {
                    $q->where('course_id', $course->id)
                      ->select('student_id', 'status');
                }]);
            }])
            ->get();
        
        $gradeValue = null;
        if (auth()->guard('student')->check()) {
            $testResult = TestResult::where('test_task_id', $testTask->id)
                            ->where('student_id', auth()->guard('student')->id())
                            ->first();
            $gradeValue = $testResult ? $testResult->score : null;
        }

        return view('show.course-test', compact('testTask', 'course', 'groups', 'testResult', 'gradeValue'));
    }

    public function submitTest(Course $course, TestTask $testTask, Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer|exists:test_answers,id'
        ]);

        $existingResult = TestResult::where('test_task_id', $testTask->id)
                              ->where('student_id', Auth::id())
                              ->exists();

        if ($existingResult) {
            return back()->with('error', 'Вы уже проходили этот тест.');
        }

        if (now() < $testTask->from || now() > $testTask->deadline) {
            return back()->with('error', 'Время для прохождения теста истекло.');
        }

        $correctAnswers = 0;
        $selectedAnswers = [];

        foreach ($request->answers as $questionId => $answerId) {
            $isCorrect = $testTask->questions()
                ->whereHas('answers', function($q) use ($answerId) {
                    $q->where('id', $answerId)
                      ->where('is_correct', true);
                })
                ->exists();

            if ($isCorrect) {
                $correctAnswers++;
            }

            $selectedAnswers[$questionId] = $answerId;
        }

        $score = round(($correctAnswers / $testTask->questions->count()) * 100);

        TestResult::create([
            'test_task_id' => $testTask->id,
            'student_id' => Auth::id(),
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $testTask->questions->count(),
            'answers' => $selectedAnswers
        ]);

        return redirect()->route('CourseTestTask', ['course' => $course->id, 'testTask' => $testTask->id])
               ->with('success', 'Тест успешно пройден! Ваша оценка: ' . $score . '/100');
    }
    
    public function edit(Course $course, Task $task) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) 
        {
            abort(403);
        }
        $editing = true;
        return view('show.course-task', compact('course', 'task', 'editing'));
    }

    public function update(Request $request, Course $course, Task $task) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) 
        {
            abort(403);
        }
        try {
            $validated = $request->validate(
            [
                'name' => 'required',
                'description' => 'nullable',
                'from' => 'nullable|date',
                'deadline' => 'nullable|date|after_or_equal:from',
                'new_files.*' => 'nullable|file',
                'existing_files' => 'nullable|array',
                'deleted_files' => 'nullable|string', 
            ], 
            [
                'name.required' => 'Название задания обязательно.',
                'from.date' => 'Дата начала должна быть корректной.',
                'from.after_or_equal' => 'Дата начала не может быть раньше сегодняшнего дня.',
                'deadline.date' => 'Дата дедлайна должна быть корректной.',
                'deadline.after_or_equal' => 'Дата дедлайна должна быть после даты начала.',
                'new_files.*.file' => 'Файл должен быть корректным.',
            ]);
    
            $task->update($validated);
    
            if ($request->has('deleted_files')) 
            {
                $deletedFiles = explode(',', $request->input('deleted_files'));
                foreach ($deletedFiles as $fileId) {
                    $file = TeacherTaskFile::find($fileId);
                    if ($file) {
                        Storage::delete('public/' . $file->file_path); 
                        $file->delete(); 
                    }
                }
            }
    
            if ($request->hasFile('new_files')) 
            {
                foreach ($request->file('new_files') as $file) {
                    $path = $file->store('task_files', 'public');
                    Log::info('Creating TeacherTaskFile:', [
                        'task_id' => $task->id,
                        'teacher_id' => Auth::id(),
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                    TeacherTaskFile::create([
                        'task_id' => $task->id,
                        'teacher_id' => Auth::id(),
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
    
            return back()->with('success', 'Задание успешно обновлено.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при обновлении задания.')->withInput();
        }
    }

    public function destroy(Course $course, Task $task) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) 
        {
            abort(403);
        }
        $task->delete();
        return redirect()->route('CourseTasks', compact('course'))->with('success', 'Задание успешно удалено');
    }

    public function destroyTest(Course $course, TestTask $testTask) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) 
        {
            abort(403);
        }
        $testTask->delete();
        return redirect()->route('CourseTasks', compact('course'))->with('success', 'Тест успешно удален');
    }

    public function upload(Course $course, Task $task, Request $request) 
    {
        if (!auth()->guard('student')->check()) {
            abort(403, 'Только студенты могут отправлять задания.');
        }

        try {
            $validatedData = $request->validate(
            [
                'files.*' => 'nullable|file',
                'comment' => 'nullable|max:1000'
            ], 
            [
                'files.*.file' => 'Файл должен быть корректным.',
                'comment' => 'Максимальная длина комментария: 1000 символов'
            ]);

            if (!$request->hasFile('files') && !$request->filled('comment')) {
                return back()->with('error', 'Нужно загрузить файл или оставить комментарий.')->withInput();
            }

            // Получаем ID студента
            $studentId = auth()->guard('student')->id();
            if (!$studentId) {
                // Если пользователь - админ, но должен быть студент
                return back()->with('error', 'Только студенты могут отправлять задания.')->withInput();
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files', []) as $file) {
                    $path = $file->store('student_task_files', 'public');
                    StudentTaskFile::create([
                        'task_id' => $task->id,
                        'student_id' => $studentId,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            if ($request->filled('comment')) {
                TaskComment::create([
                    'task_id' => $task->id,
                    'student_id' => $studentId,
                    'comment' => $validatedData['comment']
                ]);
            }

            return back()->with('success', 'Ваш ответ успешно отправлен.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при отправке задания.')->withInput();
        }
    }

    public function showStudentTask(Course $course, Task $task, Student $student) 
    {
        $task->load('comments');

        $groups = Group::whereHas('students', function ($query) use ($course) {
            $query->whereHas('courses', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            });
        })->with(['students' => function ($query) use ($course) {
            $query->with(['courses' => function ($q) use ($course) {
                $q->where('course_id', $course->id)->select('student_id', 'status');
            }]);
        }])->get();

        $studentFiles = $task->studentFiles()->where('student_id', $student->id)->get();
        return view('show.task-student-answer', compact('course', 'task', 'student', 'studentFiles', 'groups', 'task'));
    }

    public function showStudentTest(Course $course, TestTask $testTask, Student $student)
    {
        $testResult = TestResult::where('test_task_id', $testTask->id)
                    ->where('student_id', $student->id)
                    ->firstOrFail();

        $questions = $testTask->questions()
                    ->with(['answers' => function($query) {
                        $query->select('id', 'test_question_id', 'text', 'is_correct');
                    }])
                    ->get();

        $selectedAnswers = $testResult->answers ?? [];

        return view('show.test-student-answer', [
            'course' => $course,
            'testTask' => $testTask,
            'student' => $student,
            'testResult' => $testResult,
            'questions' => $questions,
            'selectedAnswers' => $selectedAnswers
        ]);
    }

    public function gradeStudentTask(Course $course, Task $task, Student $student, Request $request) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) 
        {
            abort(403);
        }
        try {
            $validatedData = $request->validate(
            [
                'grade' => 'required|numeric|min:0|max:100',
                'comment' => 'nullable|max:1000',
            ],
            [
                'grade.numeric' => 'Оценка должна быть числом',
                'grade.required' => 'Выставьте оценку',
                'grade.min' => 'Оценка не может быть меньше 0',
                'grade.max' => 'Оценка не может быть больше 100',
                'comment.max' => 'Комментарий слишком длинный',
            ]);
    
            TaskGrade::updateOrCreate(
                ['task_id' => $task->id, 'student_id' => $student->id],
                [
                    'grade' => $validatedData['grade'],
                    'comment' => $validatedData['comment'] ?? null 
                ]
            );
    
            if($request->regrade) 
            {
                Message::create([
                    'receiver_id' => $student->id,
                    'receiver_type' => \App\Models\Student::class,
                    'sender_id' => Auth::id(),
                    'sender_type' => \App\Models\Teacher::class,
                    'message' => "Ваше решение было оценено снова.\nКурс: {$course->name}.\nЗадание: {$task->name}.\nОценка: {$validatedData['grade']}.\nКомментарий: {$validatedData['comment']}",
                    'type' => 'grade',
                    'status' => false,
                ]);
            }
            else
            {
                Message::create([
                    'receiver_id' => $student->id,
                    'receiver_type' => \App\Models\Student::class,
                    'sender_id' => Auth::id(),
                    'sender_type' => \App\Models\Teacher::class,
                    'message' => "Ваше решение было оценено.\nКурс: {$course->name}.\nЗадание: {$task->name}.\nОценка: {$validatedData['grade']}",
                    'type' => 'grade',
                    'status' => false,
                ]);
            }
            
    
            return back()->with('success', 'Задание успешно оценено.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при оценке задания.')->withInput();
        }
    }
}
