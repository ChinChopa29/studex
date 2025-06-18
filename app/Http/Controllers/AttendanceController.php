<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Course;
use App\Models\Schedule;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function show(Course $course, Schedule $lesson): View
    {
        $lesson->load(['attendances.student', 'group.students']);
        return view('show.attendance', compact('lesson', 'course'));
    }

    public function update(UpdateAttendanceRequest $request, Course $course, Schedule $lesson): RedirectResponse
    {
        $validated = $request->validated()['attendances'];  

        $this->attendanceService->updateAttendances($lesson, $validated);

        return redirect()
            ->route('CourseScheduleShowLesson', ['course' => $course->id, 'lesson' => $lesson->id])
            ->with('success', 'Посещаемость успешно обновлена');
    }
}