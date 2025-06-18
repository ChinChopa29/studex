<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Group;
use App\Models\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleService
{
    public function getScheduleData(Course $course): array
    {
        $startOfWeek = now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = now()->endOfWeek()->format('Y-m-d');

        $groupIds = auth()->guard('student')->check()
            ? Auth::user()->groups()->pluck('groups.id')
            : [];

        $lessons = Schedule::with(['group', 'classroom'])
            ->where('course_id', $course->id)
            ->when(auth()->guard('student')->check(), function($query) use ($groupIds) {
                $query->whereIn('group_id', $groupIds);
            })
            ->where(function($query) use ($startOfWeek, $endOfWeek) {
                $query->where(function($q) use ($startOfWeek, $endOfWeek) {
                    $q->whereNull('recurrence')
                    ->orWhere('recurrence', 'none');
                })->whereBetween('date', [$startOfWeek, $endOfWeek]);

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

        return [
            'course' => $course,
            'lessons' => $lessons,
            'currentWeekDates' => $currentWeekDates,
            'currentWeekStart' => $startOfWeek,
            'currentWeekEnd' => $endOfWeek,
        ];
    }

    public function getGroupsForSchedule(Course $course) 
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

        return $groups;
    }
}
