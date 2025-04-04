<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Milestone;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function index(Course $course)
    {
        $startOfWeek = now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = now()->endOfWeek()->format('Y-m-d');

        $groupIds = auth()->guard('student')->check()
            ? Auth::user()->groups()->pluck('groups.id')
            : [];

        $lessons = Schedule::where('course_id', $course->id)
            ->when(auth()->guard('student')->check(), function($query) use ($groupIds) {
                $query->whereIn('group_id', $groupIds);
            })
            ->where(function($query) use ($startOfWeek, $endOfWeek) {
                $query->where(function($q) use ($startOfWeek, $endOfWeek) {
                    $q->whereNull('recurrence')
                    ->orWhere('recurrence', 'none');
                })
                ->whereBetween('date', [$startOfWeek, $endOfWeek]);
                
                $query->orWhere(function($q) use ($startOfWeek, $endOfWeek) {
                    $q->whereNotNull('recurrence')
                    ->where('recurrence', '!=', 'none')
                    ->where('date', '<=', $endOfWeek)
                    ->where('recurrence_end_date', '>=', $startOfWeek);
                });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy([
                'date',
                function ($item) {
                    return Carbon::parse($item->start_time)->format('H:i') . '-' . 
                        Carbon::parse($item->end_time)->format('H:i');
                }
            ]);

        $currentWeekDates = [];
        $tempDate = now()->startOfWeek(); 
        
        for ($i = 0; $i < 6; $i++) {
            $currentWeekDates[$i] = $tempDate->copy()->addDays($i)->format('Y-m-d');
        }

        return view('show.schedule', [
            'course' => $course,
            'lessons' => $lessons,
            'currentWeekDates' => $currentWeekDates,
            'currentWeekStart' => $startOfWeek,
            'currentWeekEnd' => $endOfWeek
        ]);
    }

    public function show(Course $course, Schedule $lesson) {
        return view('show.schedule-lesson', compact('course', 'lesson'));
    }


    public function create(Course $course)
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

        return view('add.create-lesson', [
            'course' => $course,
            'milestones' => $course->milestones,
            'groups' => $groups
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:lecture,practice,lab,seminar,exam,consultation',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'group_id' => 'required|exists:groups,id',
            'classroom' => 'nullable|string|max:20', 
            'recurrence' => 'required|in:none,weekly,biweekly',
            'milestone_id' => 'required_if:recurrence,weekly,biweekly|exists:milestones,id',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id'
        ]);

        $validated['teacher_id'] = Auth::id();

        if (!$this->isTimeSlotAvailable(
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['classroom']
        )) {
            return back()->withErrors(['time' => 'Это время уже занято в выбранной аудитории']);
        }

        if ($request->recurrence !== 'none') {
            $this->createRecurringEvents($validated);
        } else {
            Schedule::create($validated);
        }

        return redirect()->route('CourseSchedule', $request->course_id)
            ->with('success', 'Занятие успешно создано');
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

            $groupIds = auth()->guard('student')->check()
                ? Auth::user()->groups()->pluck('groups.id')
                : [];

            $lessons = Schedule::with('group') // Добавляем загрузку группы
                ->where('course_id', $course)
                ->when(!empty($groupIds), function($query) use ($groupIds) {
                    $query->whereIn('group_id', $groupIds);
                })
                ->where(function($query) use ($validated) {
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
            ->where('classroom', $classroom)
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

}
