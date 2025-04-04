<?php

namespace App\Http\Controllers;

use App\Models\LessonMaterial;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonMaterialController extends Controller
{
    public function store(Request $request, $courseId, $lessonId)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Макс 10MB
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $storagePath = "materials/{$lessonId}";
        Storage::makeDirectory($storagePath);

        $file = $request->file('file');
        $path = $file->store($storagePath, 'public'); 

        LessonMaterial::create([
            'lesson_id' => $lessonId,
            'name' => $request->name,
            'path' => $path, 
            'description' => $request->description
        ]);

        return back()->with('success', 'Материал успешно добавлен');
    }

    public function destroy($courseId, $lessonId, $materialId)
    {
        $material = LessonMaterial::findOrFail($materialId);
        
        if (Storage::disk('public')->exists($material->path)) {
            Storage::disk('public')->delete($material->path);
        }
        
        $material->delete();

        return back()->with('success', 'Материал удален');
    }
}
