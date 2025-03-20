<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Task;
use App\Models\TeacherTaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index(Course $course) {
        $tasks = Task::where('course_id', $course->id)->get();
        return view('show.course-tasks', compact('course', 'tasks'));
    }

    public function create(Course $course) {
        return view('add.create-task', compact('course'));
    }


    public function store(Request $request, Course $course) {
        try {
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
    
            $task = Task::create([
                'course_id' => $course->id,
                'name' => $request->name,
                'description' => $request->description,
                'from' => $request->from,
                'deadline' => $request->deadline,
            ]);
    
            if ($request->hasFile('files')) {
                foreach ($request->file('files', []) as $file) {
                    $path = $file->store('task_files', 'public');
                    TeacherTaskFile::create([
                        'task_id' => $task->id,
                        'teacher_id' => Auth::id(),
                        'file_path' => $path,
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

    public function show(Course $course, Task $task) {
        return view('show.course-task', compact('task', 'course'));
    }
    
 
}
