<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Services\AccountingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountingController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    private function getAuthenticatedUser()
    {
        return Auth::guard('admin')->user()
            ?? Auth::guard('teacher')->user()
            ?? Auth::guard('student')->user();
    }

    private function handleGroupView(Course $course, string $viewName): View
    {
        $course->load(['groups.students']);

        $groups = $this->accountingService->getGroupWithStudents($course);

        return view("accounting.$viewName", [
            'course' => $course,
            'groups' => $groups,
            'user' => $this->getAuthenticatedUser()
        ]);
    }

    private function handleExport(Course $course, Request $request, string $serviceMethod)
    {
        $groupId = $request->input('group');

        try {
            $filePath = $this->accountingService->$serviceMethod($course, $groupId);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'Файл не найден на сервере.');
            }

            return response()->download($filePath)->deleteFileAfterSend();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Выбранная группа не найдена.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Не удалось экспортировать файл: ' . $e->getMessage());
        }
    }

    private function handleStudentView(Course $course, Student $student, Request $request, string $serviceMethod, string $viewName)
    {
        try {
            $data = $this->accountingService->$serviceMethod(
                $course,
                $student,
                $request->input('group_id'),
                15 
            );
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }

        $data['user'] = $this->getAuthenticatedUser();

        return view("accounting.student.$viewName", $data);
    }

    private function handleStudentExport(Course $course, Student $student, Request $request, string $serviceMethod)
    {
        $groupId = $request->input('group_id');

        try {
            $filePath = $this->accountingService->$serviceMethod($course, $student, $groupId);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'Файл не найден на сервере.');
            }

            return response()->download($filePath)->deleteFileAfterSend();
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Выбранная группа не найдена.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Не удалось экспортировать файл: ' . $e->getMessage());
        }
    }

    public function attendance(Course $course): View
    {
        return $this->handleGroupView($course, 'attendance');
    }

    public function assignment(Course $course) 
    {
        return $this->handleGroupView($course, 'assignment');
    }

    public function performance(Course $course)
    {
        return $this->handleGroupView($course, 'performance');
    }

    public function exportAttendance(Course $course, Request $request)
    {
        return $this->handleExport($course, $request, 'exportGroupAttendance');
    }   

    public function exportAssignment(Course $course, Request $request) 
    {
        return $this->handleExport($course, $request, 'exportGroupAssignment');
    }

    public function exportPerformance(Course $course, Request $request)
    {
        return $this->handleExport($course, $request, 'exportGroupPerfomance');
    }

    public function studentAttendance(Course $course, Student $student, Request $request)
    {
        return $this->handleStudentView($course, $student, $request, 'showStudentAttendance', 'attendance');
    }

    public function studentAssignment(Course $course, Student $student, Request $request)
    {
        return $this->handleStudentView($course, $student, $request, 'showStudentAssignment', 'assignment');
    }
    
    public function exportStudentAttendance(Course $course, Student $student, Request $request)
    {
        return $this->handleStudentExport($course, $student, $request, 'exportStudentAttendance');
    }

    public function exportStudentAssignment(Course $course, Student $student, Request $request)
    {
        return $this->handleStudentExport($course, $student, $request, 'exportStudentAssignment');
    }
}
