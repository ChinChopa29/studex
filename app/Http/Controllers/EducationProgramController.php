<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducationProgramRequest;
use App\Models\EducationProgram;
use App\Services\EducationProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EducationProgramController extends Controller
{
    protected const PAGINATION_PER_PAGE = 10;

    protected $educationProgramService;

    public function __construct(EducationProgramService $educationProgramService)
    {
        $this->educationProgramService = $educationProgramService;
    }

    public function index(): View
    {
        $educationPrograms = EducationProgram::paginate(self::PAGINATION_PER_PAGE);
        return view('admin.education-programs', compact('educationPrograms'));
    }

    public function show(EducationProgram $educationProgram): View
    {
        return view('admin.show.education-program', compact('educationProgram'));
    }

    public function create() : View
    {
        return view('admin.add.add-program');
    }

    public function store(EducationProgramRequest $request): RedirectResponse 
    {
        $this->educationProgramService->createEducationProgram($request->validated());
        return redirect()->back()->with('success', 'Образовательная программа успешно создана');
    }

    public function edit(EducationProgram $educationProgram): View
    {
        $editing = true;
        return view('admin.show.education-program', compact('educationProgram', 'editing'));
    }

    public function update(EducationProgram $educationProgram, EducationProgramRequest $request): RedirectResponse
    {
        $this->educationProgramService->updateEducationProgram($educationProgram, $request->validated());
        return redirect()
            ->route('admin.showProgram', $educationProgram->id)
            ->with('success', 'Образовательная программа успешно обновлена');
    }

    public function destroy(EducationProgram $educationProgram): RedirectResponse
    {
        $this->educationProgramService->deleteEducationProgram($educationProgram);
        return redirect()
            ->route('admin.showPrograms')
            ->with('success', 'Образовательная программа успешно удалена');
    }
    
    public function search(Request $request): View
    {
        $filters = $request->only(['search', 'degree', 'mode', 'duration']);
        $educationPrograms = $this->educationProgramService->searchEducationProgram($filters);
        return view('admin.education-programs', compact('educationPrograms'));
    }

}
