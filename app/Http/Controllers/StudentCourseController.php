<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseStyle;
use App\Models\Group;
use App\Services\StudentCourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
    protected $studentCourseService;

    public function __construct(StudentCourseService $studentCourseService)
    {
        $this->studentCourseService = $studentCourseService;
    }

    public function index() 
    {
        $student = Auth::user(); 
        $courses = $this->studentCourseService->getCourses($student);
        return view('courses', compact('courses'));
    }

    public function show(Course $course) 
    {
        return view('show.course', compact('course'));
    }

    public function studentsShow(Course $course) 
    {
        $groups = $this->studentCourseService->getGroups($course);
        return view('show.course-students', compact('course', 'groups'));
    }

    public function storeColor(Request $request)
    {
        $data = $request->validate([
            'color' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        CourseStyle::updateOrCreate(
            ['course_id' => $data['course_id']],
            [
                'color' => $data['color'],
                'student_id' => Auth::id()
            ]
        );

        return back()->with('success', 'Цвет обновлён!');
    }
}
