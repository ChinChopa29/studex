<?php

namespace App\Services;

use App\Models\Classroom;

class ClassroomService
{
   public function createClassroom(array $validated)
   {
       $classroom = Classroom::create([
            'number' => $validated['number'],
            'capacity' => $validated['capacity'],
            'type' => $validated['type'],
            'computers' => $validated['computers'] ?? 0,
        ]);

        if (!empty($validated['education_program_ids'])) {
            $classroom->educationPrograms()->sync($validated['education_program_ids']);
        }

        return $classroom;
   }
}