<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassroomRequest;
use App\Models\EducationProgram;
use App\Services\ClassroomService;

class ClassroomController extends Controller
{
    protected $classroomService;

    public function __construct(ClassroomService $classroomService)
    {
        $this->classroomService = $classroomService;
    }
    
    public function create()
    {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-classroom', compact('educationPrograms'));
    }

    public function store(StoreClassroomRequest $request)
    {
        $validated = $request->validated();

        $this->classroomService->createClassroom($validated);
        return redirect()->route('admin.createClassroom')->with('success', 'Кабинет успешно добавлен.');
    }
}
