<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
   public function index() {
      $student = Auth::user(); 
  
      $courses = Course::whereHas('students', function ($query) use ($student) {
          $query->where('students.id', $student->id)
                ->where('student_course.status', 'accepted'); 
      })->get();
  
      return view('courses', compact('courses'));
  }
  
   public function show(Course $course) {
      return view('show.course', compact('course'));
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
  
}
