<?php

namespace App\Services;

use App\Models\EducationProgram;

class EducationProgramService
{
   public function createEducationProgram(array $data) 
   {
      return EducationProgram::create($data);
   }

   public function updateEducationProgram(EducationProgram $program, array $data)
   {
      return $program->update($data);
   }

   public function deleteEducationProgram(EducationProgram $program)
   {
      return $program->delete();
   }

   public function searchEducationProgram(array $filters)
   {
      $query = $filters['search'] ?? null;
      $degree = $filters['degree'] ?? null;
      $mode = $filters['mode'] ?? null;
      $duration = $filters['duration'] ?? null;

      $educationPrograms = EducationProgram::query();

      if ($query) {
         $educationPrograms->where(function ($q) use ($query) {
             $q->where('title', 'LIKE', "%$query%")
             ->orWhere('degree', 'LIKE', "%$query%")
             ->orWhere('mode', 'LIKE', "%$query%")
             ->orWhere('duration', 'LIKE', "%$query%");
         });
      }

      if ($degree) $educationPrograms->where('degree', $degree);
      if ($mode) $educationPrograms->where('mode', $mode);
      if ($duration) $educationPrograms->where('duration', $duration);

      return $educationPrograms->paginate(10)->appends(request()->query());
   }
}