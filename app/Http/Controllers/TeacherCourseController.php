<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherCourseController extends Controller
{
    public function index() {
        $teacher = Auth::user(); 
        if ($teacher) {
            $courses = $teacher->courses()->get(); 
            return view('courses', compact('courses'));
        } else {
            return redirect()->route('login')->with('error', 'Преподаватель не найден.');
    }
    }

    public function show(Course $course) {
        return view('show.course', compact('course'));
    }

    public function inviteStudentsForm(Course $course) {
        $groups = Group::all();
        return view('add.invite-students-to-course', compact('course', 'groups'));
    }

    public function inviteStudents(Request $request, Course $course) { 
        $groupId = $request->input('groupId');
        $group = Group::find($groupId); 
        $teacher = Auth::user(); 
    
        if (!$group) {
            return redirect()->back()->with('error', 'Группа не найдена');
        }
    
        $invitedCount = 0;
        $skippedCount = 0;
    
        foreach ($group->students as $student) { 
            $alreadyInvited = DB::table('student_course')
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->exists();
    
            $alreadyEnrolled = DB::table('student_course')
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->where('status', 'accepted')
                ->exists();
    
            if ($alreadyInvited || $alreadyEnrolled) {
                $skippedCount++;
                continue; 
            }
    
            DB::table('student_course')->insert([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'pending', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            $message = new Message([
                'message' => 'Вы были приглашены на курс "'.$course->name.'".',
                'type' => 'invite',
                'status' => false,
                'additional' => $course->id,
            ]);
    
            $message->sender()->associate($teacher);
            $message->receiver()->associate($student); 
            $message->save();   
    
            $invitedCount++;
        }
    
        return redirect()->route('teacherCourseInviteForm', ['course' => $course->id])
            ->with('success', "Приглашения отправлены: $invitedCount. Пропущено: $skippedCount.");
    }
    
    public function getGroupStudents(Request $request) {
        if (!$request->group_id) {
            return response()->json(['error' => 'Не передан ID группы'], 400);
        }
    
        $students = Student::whereHas('groups', function ($query) use ($request) {
            $query->where('groups.id', $request->group_id);
        })->get();
    
        if ($students->isEmpty()) {
            return '<p class="text-gray-500">В этой группе пока нет студентов.</p>';
        }
    
        $html = '<ul class="list-disc pl-5">';
        foreach ($students as $student) {
            $html .= "<li>{$student->surname} {$student->name} {$student->lastname}</li>";
        }
        $html .= '</ul>';
    
        return $html;
    }

    public function studentsShow(Course $course) {
        Log::info('Course found: ', ['id' => $course->id]);
        $groups = Group::whereHas('students', function ($query) use ($course) {
            $query->whereHas('courses', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            });
        })->with(['students' => function ($query) use ($course) {
            $query->with(['courses' => function ($q) use ($course) {
                $q->where('course_id', $course->id)->select('student_id', 'status');
            }]);
        }])->get();
    
        return view('show.course-students', compact('course', 'groups'));
    }

    public function gradesShow(Course $course) {
        return view('show.course-grades', compact('course'));
    }
    
}
