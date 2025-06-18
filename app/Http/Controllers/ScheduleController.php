<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\Milestone;
use App\Models\Schedule;
use App\Services\ScheduleGeneratorService;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Course $course): View
    {
        $data = $this->scheduleService->getScheduleData($course);
        return view('show.schedule', $data);
    }

    public function show(Course $course, Schedule $lesson): View
    {
        return view('show.schedule-lesson', compact('course', 'lesson'));
    }

    public function edit(Course $course, Schedule $lesson): View
    {
        $editing = true;
        $groups = Group::all(); 
        $tasks = $course->tasks; 
        return view('show.schedule-lesson', compact('course', 'lesson', 'editing', 'groups', 'tasks'));
    }

    public function create(Course $course)
    {
        $groups = $this->scheduleService->getGroupsForSchedule($course);

        return view('add.create-lesson', [
            'course' => $course,
            'milestones' => $course->milestones,
            'groups' => $groups
        ]);
    }

    public function store(StoreScheduleRequest $request)
    {
        $validated = $request->validated();

        $validated['teacher_id'] = Auth::id();

        $classroom = \App\Models\Classroom::where('number', $validated['classroom'])->first();
        if (!$classroom) {
            return back()->withErrors(['classroom' => 'Кабинет с таким номером не найден']);
        }
        $validated['classroom_id'] = $classroom->id;
        unset($validated['classroom']);

        if (!$this->isTimeSlotAvailable(
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['classroom_id']
        )) {
            return back()->withErrors(['time' => 'Это время уже занято в выбранной аудитории']);
        }

        if ($request->recurrence !== 'none') {
            $this->createRecurringEvents($validated);
        } else {
            Schedule::create($validated);
        }

        if ($request->recurrence === 'none' && $request->has('task_id')) {
            $validated['task_id'] = $request->task_id;
        } else {
            $validated['task_id'] = null;
        }

        return redirect()->route('CourseSchedule', $request->course_id)
            ->with('success', 'Занятие успешно создано');
    }

    public function update(UpdateScheduleRequest $request, Course $course, Schedule $lesson)
    {
        $request->merge([
            'start_time' => Carbon::parse($request->start_time)->format('H:i'),
            'end_time' => Carbon::parse($request->end_time)->format('H:i')
        ]);

        $validated = $request->validated();

        $lesson->update($validated);

        return redirect()->route('CourseScheduleShowLesson', ['course' => $course->id, 'lesson' => $lesson->id])
            ->with('success', 'Занятие успешно обновлено');
    }

    public function destroy(Course $course, Schedule $lesson) 
    {
        $lesson->delete();
        return redirect()->route('CourseSchedule', ['course' => $course->id])->with('success', 'Занятие успешно удалено');
    }

    private function createRecurringEvents($data)
    {
        $startDate = Carbon::parse($data['date']);
        $milestone = Milestone::find($data['milestone_id']);
        $endDate = Carbon::parse($milestone->deadline);
        
        if ($startDate->gt($endDate)) {
            return;
        }
        
        $interval = $data['recurrence'] === 'weekly' ? 1 : 2;
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            Schedule::create([
                'title' => $data['title'],
                'type' => $data['type'],
                'date' => $currentDate->format('Y-m-d'),
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'classroom' => $data['classroom'],
                'group_id' => $data['group_id'],
                'course_id' => $data['course_id'],
                'description' => $data['description'],
                'recurrence' => $data['recurrence'],
                'recurrence_end_date' => $endDate,
                'teacher_id' => $data['teacher_id'],
                'milestone_id' => $data['milestone_id']
            ]);
            
            $currentDate->addWeeks($interval);
        }
    }

    public function getLessons(Request $request, $course)
    {
        try {
            $validated = $request->validate([
                'start' => 'required|date',
                'end' => 'required|date|after_or_equal:start'
            ]);

            $query = Schedule::with(['group', 'classroom'])
                ->where('course_id', $course);

            if (auth()->guard('student')->check()) {
                $groupIds = Auth::user()->groups()->pluck('groups.id')->toArray();
                $query->whereIn('group_id', $groupIds);
            }

            $lessons = $query->where(function($query) use ($validated) {
                    $query->where(function($q) use ($validated) {
                        $q->whereNull('recurrence')
                        ->orWhere('recurrence', 'none');
                    })
                    ->whereBetween('date', [$validated['start'], $validated['end']]);
                    
                    $query->orWhere(function($q) use ($validated) {
                        $q->whereNotNull('recurrence')
                        ->where('recurrence', '!=', 'none')
                        ->where('date', '<=', $validated['end'])
                        ->where('recurrence_end_date', '>=', $validated['start']);
                    });
                })
                ->orderBy('date')
                ->orderBy('start_time')
                ->get()
                ->groupBy([
                    fn($item) => Carbon::parse($item->date)->format('Y-m-d'),
                    fn($item) => Carbon::parse($item->start_time)->format('H:i') . '-' . 
                                Carbon::parse($item->end_time)->format('H:i')
                ]);

            return response()->json($lessons);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function isTimeSlotAvailable($date, $startTime, $endTime, $classroom, $ignoreId = null)
    {
        return !Schedule::where('date', $date)
            ->where('classroom_id', $classroom)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                      });
            })
            ->when($ignoreId, function($query, $ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();
    }

    private function applyAccessRestrictions($query)
    {
        $user = Auth::user();
        
        if (auth()->guard('student')->check()) {
            $query->where('group_id', $user->group_id);
        } 
        elseif (auth()->guard('teacher')->check()) {
            $query->where('teacher_id', $user->id);
        }
        
        return $query;
    }

    public function generate(Request $request, ScheduleGeneratorService $service)
    {
        $request->validate([
            'milestone' => 'required|integer|exists:milestones,id',
        ]);

        $milestone = Milestone::findOrFail($request->milestone);

        $result = $service->generateForMilestone($milestone);

        if ($result['errors'] > 0) {
            return redirect()->back()->with('error', 'Ошибка при генерации расписания. Проверьте логи.');
        }

        if ($result['created'] > 0) {
            return redirect()->back()->with('success', "Расписание успешно сгенерировано! Добавлено занятий: {$result['created']}");
        }

        if ($result['already_ready'] > 0) {
            return redirect()->back()->with('success', 'Расписание уже было сгенерировано и заполнено полностью.');
        }

        return redirect()->back()->with('info', 'Нет данных для генерации расписания.');
    }
}
