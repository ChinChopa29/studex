<?php

namespace App\Services;

use App\Models\Milestone;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\Classroom;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScheduleGeneratorService
{
    protected array $timeSlots = [
        ['start' => '08:00', 'end' => '08:50'],
        ['start' => '09:00', 'end' => '09:50'],
        ['start' => '10:00', 'end' => '10:50'],
        ['start' => '11:00', 'end' => '11:50'],
        ['start' => '12:20', 'end' => '13:10'],
        ['start' => '13:20', 'end' => '14:10'],
        ['start' => '14:20', 'end' => '15:10'],
        ['start' => '15:20', 'end' => '16:10'],
        ['start' => '16:20', 'end' => '17:10']
    ];

    public function generateForMilestone(Milestone $milestone): array
    {
        $result = [
            'created' => 0,
            'already_ready' => 0,
            'errors' => 0,
        ];

        try {
            $courses = Course::whereHas('milestones', fn ($q) => $q->where('id', $milestone->id))->get();

            foreach ($courses as $course) {
                $groupIds = Group::whereHas('students', function ($query) use ($course) {
                    $query->whereHas('courses', fn ($q) => $q->where('course_id', $course->id));
                })->whereNull('subgroup')->pluck('id');

                $groups = Group::whereIn('id', $groupIds)->get();
                $teacher = $course->teachers()->first();
                $weeks = Carbon::parse($milestone->from)->diffInWeeks(Carbon::parse($milestone->deadline));

                foreach ($groups as $group) {
                    $totalHours = $course->hours;
                    $lectureTarget = floor($totalHours / 2);
                    $practiceTarget = $totalHours - $lectureTarget;

                    $existingLectures = Schedule::where([
                        'course_id' => $course->id,
                        'milestone_id' => $milestone->id,
                        'group_id' => $group->id,
                        'type' => 'lecture',
                    ])->count();

                    $existingPractices = Schedule::where([
                        'course_id' => $course->id,
                        'milestone_id' => $milestone->id,
                        'group_id' => $group->id,
                        'type' => 'practice',
                    ])->count();

                    $remainingLectures = max(0, $lectureTarget - $existingLectures);
                    $remainingPractices = max(0, $practiceTarget - $existingPractices);

                    if ($remainingLectures === 0 && $remainingPractices === 0) {
                        // Уже всё сгенерировано для этой группы
                        $result['already_ready']++;
                        continue;
                    }

                    for ($week = 0; $week < $weeks; $week++) {
                        $date = Carbon::parse($milestone->from)->addWeeks($week)->startOfWeek(Carbon::MONDAY);

                        foreach (['lecture', 'practice'] as $type) {
                            if (
                                ($type === 'lecture' && $remainingLectures <= 0) ||
                                ($type === 'practice' && $remainingPractices <= 0)
                            ) {
                                continue;
                            }

                            $slot = $this->findAvailableSlot($date, $teacher->id, $group->id, $type);

                            if (!$slot) {
                                continue;
                            }

                            Schedule::create([
                                'title' => $course->name,
                                'description' => null,
                                'type' => $type,
                                'date' => $slot['date'],
                                'start_time' => $slot['start'],
                                'end_time' => $slot['end'],
                                'classroom_id' => $slot['classroom_id'],
                                'teacher_id' => $teacher->id,
                                'group_id' => $group->id,
                                'course_id' => $course->id,
                                'milestone_id' => $milestone->id,
                            ]);

                            $result['created']++;

                            if ($type === 'lecture') {
                                $remainingLectures--;
                            } else {
                                $remainingPractices--;
                            }

                            if ($remainingLectures <= 0 && $remainingPractices <= 0) {
                                break 2;
                            }
                        }
                    }
                }
            }

            return $result;

        } catch (\Throwable $e) {
            Log::error('Ошибка при генерации расписания: ' . $e->getMessage());
            $result['errors']++;
            return $result;
        }
    }

    protected function findAvailableSlot(Carbon $weekStart, int $teacherId, int $groupId, string $type): ?array
    {
        $dayOffsets = range(0, 4);
        shuffle($dayOffsets); 

        foreach ($dayOffsets as $dayOffset) {
            $date = $weekStart->copy()->addDays($dayOffset);

            foreach ($this->timeSlots as $slot) {
                $start = $date->copy()->setTimeFromTimeString($slot['start']);
                $end = $date->copy()->setTimeFromTimeString($slot['end']);

                $availableClassroom = $this->findFreeClassroom($start, $end, $type);
                if (!$availableClassroom) {
                    continue;
                }

                $conflict = Schedule::where('date', $date->toDateString())
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('start_time', [$start, $end])
                        ->orWhereBetween('end_time', [$start, $end]);
                    })
                    ->where(function ($q) use ($teacherId, $groupId, $availableClassroom) {
                        $q->where('teacher_id', $teacherId)
                        ->orWhere('group_id', $groupId)
                        ->orWhere('classroom_id', $availableClassroom->id);
                    })->exists();

                if (!$conflict) {
                    return [
                        'date' => $date->toDateString(),
                        'start' => $start->format('H:i'),
                        'end' => $end->format('H:i'),
                        'classroom_id' => $availableClassroom->id,
                    ];
                }
            }
        }

        return null;
    }

    protected function findFreeClassroom(Carbon $start, Carbon $end, string $type): ?Classroom
    {
        return Classroom::all()->firstWhere(function ($classroom) use ($start, $end) {
            return !Schedule::where('classroom_id', $classroom->id)
                ->where('date', $start->toDateString())
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end]);
                })->exists();
        });
    }
}
