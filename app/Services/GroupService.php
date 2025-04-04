<?php

namespace App\Services;

use App\Models\EducationProgram;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;

class GroupService
{
   public function getAllTeachers(): Collection
   {
      return Teacher::all();
   }

   public function createGroup(array $validatedData)
   {
      $educationProgram = EducationProgram::findOrFail($validatedData['education_program_id']);
    
      $graduation_year = $validatedData['admission_year'] + $educationProgram->duration;
   
      return Group::create([
         'name' => $validatedData['name'],
         'admission_year' => $validatedData['admission_year'],
         'graduation_year' => $graduation_year,
         'education_program_id' => $validatedData['education_program_id'],
      ]);
   }

   public function createSubGroup(array $validatedData) 
   {
      $parentGroup = Group::findOrFail($validatedData['group']);
    
      Group::create([
         'name' => $validatedData['name'],
         'admission_year' => $parentGroup->admission_year, 
         'graduation_year' => $parentGroup->graduation_year, 
         'education_program_id' => $parentGroup->education_program_id, 
         'subgroup' => $parentGroup->id,
      ]);
   }

   public function updateGroup(Group $group, array $data) 
   {
      $group->update($data);
   }

   public function attachTeacher(Group $group, $teacherId)
   {
      if (!$group->teachers->contains($teacherId)) {
         $group->teachers()->attach($teacherId);
      }
   }

   public function detachTeacher(Group $group, $teacherId)
   {
      $group->teachers()->detach($teacherId);
   }

   public function attachStudent(Group $group, Student $student) 
   {
      $group->students()->attach($student->id);
      
   }

   public function detachStudent(Group $group, $studentId)
   {
      $group->students()->detach($studentId);
   }

   public function deleteGroup(Group $group)
   {
      $group->delete();
   }

   public function searchGroups($query)
   {
      $groups = Group::with('educationProgram');

      if ($query) {
         $groups->where(function ($q) use ($query) {
               $q->where('name', 'LIKE', "%$query%")
               ->orWhere('admission_year', 'LIKE', "%$query%")
               ->orWhere('graduation_year', 'LIKE', "%$query%");
         });

         $groups->orWhereHas('educationProgram', function ($q) use ($query) {
               $q->where('title', 'LIKE', "%$query%");
         });
      }

      $groups = $groups->paginate(10)->appends(request()->query());

      return $groups ?: collect(); 
   }
}