<?php

namespace App\Services;

use App\Models\Milestone;

class MilestoneService
{
   public function createMilestone($course, array $data) 
   {
      $milestone = Milestone::create([
         'course_id' => $course->id,
         'name' => "Рубежный контроль {$data['milestone_number']}",
         'milestone_number' => $data['milestone_number'],
         'from' => $data['from'],
         'deadline' => $data['deadline'],
     ]);
   }

   public function updateMilestone(Milestone $milestone, array $data)
   {
      return $milestone->update($data);
   }

   public function deleteMilestone(Milestone $milestone)
   {
      return $milestone->delete($milestone);
   }
}
