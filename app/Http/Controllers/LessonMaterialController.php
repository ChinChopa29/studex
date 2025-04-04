<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonMaterialRequest;
use App\Services\LessonMaterialService;
use Illuminate\Http\RedirectResponse;


class LessonMaterialController extends Controller
{
    protected $lessonMaterialService;

    public function __construct(LessonMaterialService $lessonMaterialService)
    {
        $this->lessonMaterialService = $lessonMaterialService;
    }

    public function store(StoreLessonMaterialRequest $request, $courseId, $lessonId): RedirectResponse
    {
        $request->validated();
        $this->lessonMaterialService->createLessonMaterial($request, $lessonId);
        return back()->with('success', 'Материал успешно добавлен');
    }

    public function destroy($courseId, $lessonId, $materialId): RedirectResponse
    {
        $this->lessonMaterialService->deleteLessonMaterial($materialId);
        return back()->with('success', 'Материал удален');
    }
}
