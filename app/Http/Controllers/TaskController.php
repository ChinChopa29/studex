<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Message;
use App\Models\Student;
use App\Models\StudentTaskFile;
use App\Models\Task;
use App\Models\TaskGrade;
use App\Models\TeacherTaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index(Course $course) 
    {
        $tasks = Task::where('course_id', $course->id)->get();
        return view('show.course-tasks', compact('course', 'tasks'));
    }

    public function create(Course $course) {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }
        return view('add.create-task', compact('course'));
    }


    public function store(Request $request, Course $course) 
    {
        Log::info('Начало выполнения метода store');

        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            Log::error('Пользователь не авторизован.');
            abort(403);
        }

        try {
            Log::info('Валидация данных...');
            
            $validatedData = $request->validate([
                'name' => 'required',
                'description' => 'nullable',
                'from' => 'nullable|date',
                'deadline' => 'nullable|date|after_or_equal:from',
                'files.*' => 'nullable|file',
            ], [
                'name.required' => 'Название задания обязательно.',
                'from.date' => 'Дата начала должна быть корректной.',
                'from.after_or_equal' => 'Дата начала не может быть раньше сегодняшнего дня.',
                'deadline.date' => 'Дата дедлайна должна быть корректной.',
                'deadline.after_or_equal' => 'Дата дедлайна должна быть после даты начала.',
                'files.*.file' => 'Файл должен быть корректным.',
            ]);

            Log::info('Данные прошли валидацию.');

            $task = Task::create([
                'course_id' => $course->id,
                'name' => $request->name,
                'description' => $request->description,
                'from' => $request->from,
                'deadline' => $request->deadline,
            ]);

            Log::info('Задача успешно создана.');

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

            Log::info('Файлы успешно загружены.');

            return back()->with('success', 'Задание успешно добавлено.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Ошибка валидации: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Ошибка при добавлении задания: '.$e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ошибка при добавлении задания.')->withInput();
        }
    }


    public function show(Course $course, Task $task) 
    {
        $task->load(['teacherFiles', 'studentFiles', 'grades']); 

        $groups = Group::whereHas('students', function ($query) use ($course) {
            $query->whereHas('courses', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            });
        })->with(['students' => function ($query) use ($course) {
            $query->with(['courses' => function ($q) use ($course) {
                $q->where('course_id', $course->id)->select('student_id', 'status');
            }]);
        }])->get();

    
        return view('show.course-task', compact('task', 'course', 'groups'));
    }
    
    public function edit(Course $course, Task $task) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }
        $editing = true;
        return view('show.course-task', compact('course', 'task', 'editing'));
    }

    public function update(Request $request, Course $course, Task $task) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }
        try {
            $validated = $request->validate([
                'name' => 'required',
                'description' => 'nullable',
                'from' => 'nullable|date',
                'deadline' => 'nullable|date|after_or_equal:from',
                'new_files.*' => 'nullable|file',
                'existing_files' => 'nullable|array',
                'deleted_files' => 'nullable|string', 
            ], [
                'name.required' => 'Название задания обязательно.',
                'from.date' => 'Дата начала должна быть корректной.',
                'from.after_or_equal' => 'Дата начала не может быть раньше сегодняшнего дня.',
                'deadline.date' => 'Дата дедлайна должна быть корректной.',
                'deadline.after_or_equal' => 'Дата дедлайна должна быть после даты начала.',
                'new_files.*.file' => 'Файл должен быть корректным.',
            ]);
    
            $task->update($validated);
    
            if ($request->has('deleted_files')) {
                $deletedFiles = explode(',', $request->input('deleted_files'));
                foreach ($deletedFiles as $fileId) {
                    $file = TeacherTaskFile::find($fileId);
                    if ($file) {
                        Storage::delete('public/' . $file->file_path); 
                        $file->delete(); 
                    }
                }
            }
    
            if ($request->hasFile('new_files')) {
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
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }
        $task->delete();
        $tasks = Task::where('course_id', $course->id)->get();
        return view('show.course-tasks', compact('course', 'tasks'))->with('success', 'Задание успешно удалено');
    }

    public function upload(Course $course, Task $task, Request $request) 
    {
        if (!auth()->guard('student')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }
        try {
            $validatedData = $request->validate([
                'files.*' => 'nullable|file',
            ], [
                'files.*.file' => 'Файл должен быть корректным.',
            ]);
    
            if ($request->hasFile('files')) {
                foreach ($request->file('files', []) as $file) {
                    $path = $file->store('student_task_files', 'public');
                    StudentTaskFile::create([
                        'task_id' => $task->id,
                        'student_id' => Auth::id(),
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
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
        return view('show.task-student-answer', compact('course', 'task', 'student', 'studentFiles', 'groups'));
    }

    public function gradeStudentTask(Course $course, Task $task, Student $student, Request $request) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }
        try {
            $validatedData = $request->validate([
                'grade' => 'required|numeric|min:0|max:100',
            ], [
                'grade.numeric' => 'Оценка должна быть числом',
                'grade.required' => 'Выставьте оценку',
                'grade.min' => 'Оценка не может быть меньше 0',
                'grade.max' => 'Оценка не может быть больше 100',
            ]);
    
            TaskGrade::updateOrCreate(
                ['task_id' => $task->id, 'student_id' => $student->id], 
                ['grade' => $validatedData['grade']] 
            );
    
            if($request->regrade) {
                Message::create([
                    'receiver_id' => $student->id,
                    'receiver_type' => \App\Models\Student::class,
                    'sender_id' => Auth::id(),
                    'sender_type' => \App\Models\Teacher::class,
                    'message' => "Ваше решение было оценено снова.\nКурс: {$course->name}.\nЗадание: {$task->name}.\nОценка: {$validatedData['grade']}",
                    'type' => 'grade',
                    'status' => false,
                ]);
            }
            else {
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
