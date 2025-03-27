<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Message;
use App\Models\Student;
use App\Models\Task;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherCourseController extends Controller
{
    public function index() {
        $admin = Auth::guard('admin')->user();
        $teacher = Auth::guard('teacher')->user();
        $student = Auth::guard('student')->user();
    
        if ($admin) {
            $courses = Course::all();
            return view('courses', compact('courses'));
        } elseif ($teacher) {
            $courses = $teacher->courses()->get();
            return view('courses', compact('courses'));
        } elseif ($student) {
            $courses = $student->courses()->get();
            return view('courses', compact('courses'));
        }
    
        return redirect()->route('login')->with('error', 'Пользователь не найден.');
    }
    
    

    public function show(Course $course) {
        $teachers = Teacher::all();
        return view('show.course', compact('course', 'teachers'));
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
    
    public function getGroupStudents(Request $request, $course, $group_id) {
        try {
            $students = Student::whereHas('groups', function($query) use ($group_id) {
                    $query->where('groups.id', $group_id);
                })
                ->leftJoin('student_course', function($join) use ($course) {
                    $join->on('students.id', '=', 'student_course.student_id')
                         ->where('student_course.course_id', $course);
                })
                ->select(
                    'students.*',
                    'student_course.status as course_status'
                )
                ->get();
    
            if ($students->isEmpty()) {
                return '<div class="p-4 text-gray-400">В этой группе пока нет студентов.</div>';
            }
    
            $html = '<div class="space-y-2">';
            foreach ($students as $student) {
                $status = 'Не приглашен';
                $statusClass = 'bg-blue-500';
                
                if ($student->course_status) {
                    switch ($student->course_status) {
                        case 'accepted':
                            $status = 'Принял';
                            break;
                        case 'pending':
                            $status = 'Ожидает';
                            break;
                        case 'declined':
                            $status = 'Отклонил';
                            break;
                    }
                }
                
                $html .= '
                <div class="flex items-center justify-between p-3 bg-gray-600 rounded-lg hover:bg-gray-500 transition-colors">
                    <div>
                        <span class="font-medium">'.$student->surname.' '.$student->name.' '.$student->lastname.'</span>
                        <div class="text-xs text-gray-300 mt-1">'.$student->email.'</div>
                    </div>
                    <span class="text-xs text-white px-2 py-1 rounded-full '.$statusClass.'">'.$status.'</span>
                </div>';
            }
            $html .= '</div>';
    
            return $html;
        } catch (\Exception $e) {
            Log::error('Error fetching group students: '.$e->getMessage());
            return '<div class="bg-red-900/50 text-red-300 p-4 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Ошибка загрузки студентов
                </div>';
        }
    }

    public function studentsShow(Course $course) {
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
