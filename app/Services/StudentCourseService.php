<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Group;
use App\Models\Student;

class StudentCourseService 
{
   public function getCourses(Student $student)
   {
      $courses = Course::whereHas('students', function ($query) use ($student) {
         $query->where('students.id', $student->id)
                  ->where('student_course.status', 'accepted'); 
      })->get();

      return $courses;
   }

   public function getGroups(Course $course)
   {
      $groups = Group::whereHas('students', function ($query) use ($course) {
            $query->whereHas('courses', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            });
        })->with(['students' => function ($query) use ($course) {
            $query->with(['courses' => function ($q) use ($course) {
                $q->where('course_id', $course->id)->select('student_id', 'status');
            }]);
        }])->get();

      return $groups;
   }
}