<?php

namespace App\Http\Controllers;

use App\Models\Course;
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
  
}
