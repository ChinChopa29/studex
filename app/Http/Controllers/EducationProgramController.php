<?php

namespace App\Http\Controllers;

use App\Models\EducationProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EducationProgramController extends Controller
{
    public function create() {
        return view('admin.add.add-program');
    }

    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'title' => 'required|min:5|max:150',
                'acronym' => 'nullable',
                'description' => 'required|min:50|max:3000',
                'degree' => 'required|in:Бакалавриат,Магистратура,Аспирантура',
                'duration' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $validDurations = [
                            'Бакалавриат' => [3, 4],
                            'Магистратура' => [1, 2],
                            'Аспирантура' => [3, 4, 5]
                        ];
                        $degree = $request->degree;
                        if (isset($validDurations[$degree]) && !in_array($value, $validDurations[$degree])) {
                            $fail("Недопустимая длительность для выбранной степени.");
                        } elseif (!isset($validDurations[$degree])) {
                            $fail("Некорректная степень обучения.");
                        }
                    }
                ],
                'mode' => 'required|in:Очная,Очно-заочная,Дистанционная',
                'price' => 'required|numeric|min:0',
            ]);
            
            EducationProgram::create($validated);
            
            return redirect()->back()->with('success', 'Образовательная программа успешно создана');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Произошла ошибка. Пожалуйста, попробуйте снова.'])->withInput();
        }
    }

    public function index() {
        $educationPrograms = EducationProgram::paginate(10);
        return view('admin.education-programs', compact('educationPrograms'));
    }

    public function show(EducationProgram $educationProgram) {
        return view('admin.show.education-program', compact('educationProgram'));
    }

    public function edit(EducationProgram $educationProgram) {
        $editing = true;
        return view('admin.show.education-program', compact('educationProgram', 'editing'));
    }

    public function update(EducationProgram $educationProgram, Request $request) {
        try {
            $validated = $request->validate([
                'title' => 'required|min:5|max:150',
                'acronym' => 'nullable',
                'description' => 'required|min:50|max:3000',
                'degree' => 'required|in:Бакалавриат,Магистратура,Аспирантура',
                'duration' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $validDurations = [
                            'Бакалавриат' => [3, 4],
                            'Магистратура' => [1, 2],
                            'Аспирантура' => [3, 4, 5]
                        ];
                        $degree = $request->degree;
                        if (isset($validDurations[$degree]) && !in_array($value, $validDurations[$degree])) {
                            $fail("Недопустимая длительность для выбранной степени.");
                        } elseif (!isset($validDurations[$degree])) {
                            $fail("Некорректная степень обучения.");
                        }
                    }
                ],
                'mode' => 'required|in:Очная,Очно-заочная,Дистанционная',
                'price' => 'required|numeric|min:0',
            ]);
            
            $educationProgram->update($validated);
    
            return redirect()->route('admin.showProgram', $educationProgram->id)
                             ->with('success', 'Образовательная программа успешно обновлена');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Произошла ошибка. Пожалуйста, попробуйте снова.'])->withInput();
        }
    }

    public function destroy(EducationProgram $educationProgram) {
        $educationProgram->delete();
        $educationPrograms = EducationProgram::all();
        return view('admin.education-programs', compact('educationPrograms'))->with('success', 'Образовательная программа успешно удалена');
    }
    
    public function search(Request $request)
    {
        $query = $request->get('search');  
        $degree = $request->get('degree'); 
        $mode = $request->get('mode'); 
        $duration = $request->get('duration'); 

        $educationPrograms = EducationProgram::query();

        if ($query) {
            $educationPrograms->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%$query%")
                ->orWhere('degree', 'LIKE', "%$query%")
                ->orWhere('mode', 'LIKE', "%$query%")
                ->orWhere('duration', 'LIKE', "%$query%");
            });
        }

        if ($degree) {
            $educationPrograms->where('degree', $degree);
        }
        if ($mode) {
            $educationPrograms->where('mode', $mode);
        }
        if ($duration) {
            $educationPrograms->where('duration', $duration);
        }

        $educationPrograms = $educationPrograms->paginate(10)->appends(request()->query());

        return view('admin.education-programs', compact('educationPrograms'));
    }

}
