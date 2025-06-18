<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Models\Course;
use App\Models\Milestone;
use App\Services\MilestoneService;

class MilestoneController extends Controller
{
    protected $milestoneService;

    public function __construct(MilestoneService $milestoneService)
    {
        $this->milestoneService = $milestoneService;
    }

    public function create(Course $course) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }
        $milestones = Milestone::where('course_id', $course->id)->get();
        return view('add.create-milestone', compact('course', 'milestones'));
    }

    public function store(StoreMilestoneRequest $request, Course $course) 
    {
        if (!auth()->guard('teacher')->check() && !auth()->guard('admin')->check()) {
            abort(403);
        }

        try {
            $this->milestoneService->createMilestone($course, $request->validated());

            return back()->with('success', 'Рубежный контроль успешно добавлен.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при добавлении рубежного контроля.')->withInput();
        }
    }

    public function update(UpdateMilestoneRequest $request, Course $course, Milestone $milestone)   
    {
        $this->milestoneService->updateMilestone($milestone, $request->validated());
        return back()->with('success', 'Рубежный контроль обновлён.');
    }

    public function destroy(Course $course, Milestone $milestone)
    {
        $this->milestoneService->deleteMilestone($milestone);
        return back()->with('success', 'Рубежный контроль удален.');
    }
}
