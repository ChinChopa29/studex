<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function show(Course $course, Schedule $lesson)
    {
        $lesson->load(['attendances.student', 'group.students']);
        return view('show.attendance', compact('lesson', 'course'));
    }

    public function update(Request $request, Course $course, Schedule $lesson)
    {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|integer',
            'attendances.*.status' => 'required|in:present,absent,late',
            'attendances.*.comment' => 'nullable|string'
        ]);

        $groupStudents = $lesson->group->students()->pluck('students.id')->toArray();
        
        $attendances = [];
        foreach ($validated['attendances'] as $data) {
            if (!in_array($data['student_id'], $groupStudents)) {
                continue;
            }
            
            $attendances[] = [
                'lesson_id' => $lesson->id,
                'student_id' => $data['student_id'],
                'group_id' => $lesson->group_id,
                'status' => $data['status'],
                'comment' => $data['comment'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::transaction(function () use ($lesson, $attendances) {
            Attendance::where('lesson_id', $lesson->id)->delete();
            Attendance::insert($attendances);
        });

        return redirect()
            ->route('CourseScheduleShowLesson', ['course' => $course->id, 'lesson' => $lesson->id])
            ->with('success', 'Посещаемость успешно обновлена');
    }
}