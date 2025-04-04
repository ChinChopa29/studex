<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\EducationProgram;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    protected const PAGINATION_PER_PAGE = 10;
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(): View
    {
        $courses = Course::paginate(self::PAGINATION_PER_PAGE);
        return view('admin.courses', compact('courses'));
    }

    public function show(Course $course): View
    {
        $teachers = $this->courseService->getAllTeachers();
        return view('admin.show.course', [
            'course' => $course,
            'teachers' => $teachers
        ]);
    }

    public function create(): View
    {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-course', compact('educationPrograms'));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $course = $this->courseService->createCourse($validated);
        return redirect()->route('admin.createCourse')->with('success', 'Курс успешно создан!');
    }

    public function edit(Course $course): View
    {
        $teachers = $this->courseService->getAllTeachers();
        $editing = true;
        return view('admin.show.course', compact('course', 'editing', 'teachers'));
    }

    public function update(Course $course, UpdateCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (!empty($validated['teacher'])) {
            $this->courseService->attachTeacher($course, $validated['teacher']);
        }

        $this->courseService->updateCourse($course, $validated);

        return redirect()
            ->route('admin.showCourse', $course->id)
            ->with('success', 'Курс успешно обновлен');
    }

    public function detachTeacherCourse($courseId, $teacherId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);
        $this->courseService->detachTeacher($course, $teacherId);

        return redirect()->back()
            ->with('success', 'Преподаватель успешно исключен из курса');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->courseService->deleteCourse($course);
        return redirect()->route('admin.showCourses')
            ->with('success', 'Курс успешно удален');
    }

    public function search(Request $request): View
    {
        $query = $request->get('search');  
        $degree = $request->get('degree'); 
        $semester = $request->get('semester'); 

        $courses = $this->courseService->searchCourses($query, $degree, $semester);

        return view('admin.courses', compact('courses'));
    }

    public function searchCode(Request $request): JsonResponse
    {
        $code = $request->query('code');
    
        if (!$code) {
            return response()->json(['error' => 'Код не передан'], 400);
        }
    
        $courses = Course::where('code', 'like', "%$code%")->get();
    
        return response()->json($courses);
    }
}
