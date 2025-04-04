<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
   public function getAllTeachers(): Collection
   {
      return Teacher::all();
   }

   public function createCourse(array $data)
   {
      $course = Course::create([
         'name' => $data['name'],
         'description' => $data['description'] ?? null,
         'credits' => $data['credits'],
         'semester' => $data['semester'],
         'type' => $data['type'],
         'code' => $data['code'],
      ]);

      $validEducationPrograms = array_map('intval', (array) $data['education_program_id']);
      $course->educationPrograms()->sync($validEducationPrograms);

      return $course;
   }

   public function updateCourse(Course $course, array $data)
   {
      $course->update([
         'name' => $data['name'],
         'description' => $data['description'] ?? null,
         'credits' => $data['credits'],
         'semester' => $data['semester'],
         'type' => $data['type'],
         'code' => $data['code'],
      ]);

      if (isset($data['education_program_id'])) {
         $validEducationPrograms = array_map('intval', (array) $data['education_program_id']);
         $course->educationPrograms()->sync($validEducationPrograms);
      }

      return $course;
   }

   public function attachTeacher(Course $course, $teacherId)
   {
      if (!$course->teachers->contains($teacherId)) {
         $course->teachers()->attach($teacherId);
      }
   }

   public function detachTeacher(Course $course, $teacherId)
   {
      $course->teachers()->detach($teacherId);
   }

   public function deleteCourse(Course $course)
   {
      $course->delete();
   }

   public function searchCourses($query, $degree, $semester) {
      $courses = Course::query();

      if ($query) {
          $courses->where(function ($q) use ($query) {
              $q->where('name', 'LIKE', "%$query%")
              ->orWhere('semester', 'LIKE', "%$query%")
              ->orWhere('degree', 'LIKE', "%$query%")
              ->orWhere('credits', 'LIKE', "%$query%") 
              ->orWhereHas('educationPrograms', function ($q) use ($query) {
                  $q->where('title', 'LIKE', "%$query%");
              })
              ->orWhereHas('teachers', function ($q) use ($query) {
                  $q->where('name', 'LIKE', "%$query%")
                      ->orWhere('surname', 'LIKE', "%$query%")
                      ->orWhere('lastname', 'LIKE', "%$query%");
          });
      });
      }

      if ($degree) {
          $courses->where('degree', $degree);
      }

      if ($semester) {
          $courses->where('semester', $semester);
      }

      return $courses->paginate(10)->appends(request()->query());
   }

}
