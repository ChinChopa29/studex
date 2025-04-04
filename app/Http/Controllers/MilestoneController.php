<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MilestoneController extends Controller
{
    public function create(Course $course) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }
        return view('add.create-milestone', compact('course'));
    }

    public function store(Request $request, Course $course) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }

        try {
            $validatedData = $request->validate([
                'milestone_number' => [
                    'required',
                    'numeric',
                    Rule::unique('milestones')->where(fn($query) => $query->where('course_id', $course->id)),
                ],
                'from' => 'required|date',
                'deadline' => 'required|date|after_or_equal:from',
            ], [
                'milestone_number.required' => 'Номер рубежного контроля обязателен.',
                'milestone_number.numeric' => 'Номер рубежного контроля должен быть числом.',
                'milestone_number.unique' => 'Рубежный контроль с таким номером уже существует.',
                'from.date' => 'Дата начала должна быть корректной.',
                'deadline.date' => 'Дата дедлайна должна быть корректной.',
                'deadline.after_or_equal' => 'Дата дедлайна должна быть после даты начала.',
            ]);

            $milestone = Milestone::create([
                'course_id' => $course->id,
                'name' => "Рубежный контроль {$validatedData['milestone_number']}",
                'milestone_number' => $validatedData['milestone_number'],
                'from' => $validatedData['from'],
                'deadline' => $validatedData['deadline'],
            ]);

            return back()->with('success', 'Рубежный контроль успешно добавлен.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Рубежный контроль с таким номером уже существует для этого курса.')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при добавлении рубежного контроля.')->withInput();
        }
    }
}
