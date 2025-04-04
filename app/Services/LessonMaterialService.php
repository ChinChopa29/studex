<?php

namespace App\Services;

use App\Models\LessonMaterial;
use Illuminate\Support\Facades\Storage;

class LessonMaterialService
{
   public function createLessonMaterial($request, $lessonId) 
   {
      $storagePath = "materials/{$lessonId}";
      Storage::makeDirectory($storagePath);

      $file = $request->file('file');
      $path = $file->store($storagePath, 'public'); 

      return LessonMaterial::create([
         'lesson_id' => $lessonId,
         'name' => $request->name,
         'path' => $path, 
         'description' => $request->description
      ]);
   }

   public function deleteLessonMaterial($materialId)
   {
      $material = LessonMaterial::findOrFail($materialId);
        
      if (Storage::disk('public')->exists($material->path)) {
         Storage::disk('public')->delete($material->path);
      }
      
      return $material->delete();
   }
}