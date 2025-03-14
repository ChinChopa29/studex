<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $alreadyInvited = Message::where('receiver_id', $student->id)
                ->where('type', 'invite')
                ->where('additional', $course->id)
                ->exists();
    
            $alreadyEnrolled = $student->courses()
                ->where('course_id', $course->id)
                ->where('status', 'accepted')
                ->exists();
    
            if ($alreadyInvited || $alreadyEnrolled) {
                $skippedCount++;
                continue; 
            }
    
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


}
