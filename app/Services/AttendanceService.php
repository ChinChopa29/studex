<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function updateAttendances($lesson, $validatedAttendances)
    {
        $groupStudents = $lesson->group->students()->pluck('students.id')->toArray();
        
        $attendances = [];
        foreach ($validatedAttendances as $data) {
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
    }
}
