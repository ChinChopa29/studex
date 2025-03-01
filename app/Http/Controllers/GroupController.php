<?php

namespace App\Http\Controllers;

use App\Models\EducationProgram;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index() {
        $groups = Group::paginate(10);
        return view('admin.groups', compact('groups'));
    }
    
    public function create() {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-group', compact('educationPrograms'));
    }

    public function createSubgroup() {
        $groups = Group::all();
        return view('admin.add.add-subgroup', compact('groups'));
    }


    public function searchAcronym(Request $request) {
        $acronym = $request->query('acronym');
            
        if (!$acronym) {
            return response()->json([]);
        }
    
        $groups = Group::where('name', 'LIKE', "$acronym%")->get(['id', 'name']);
        
        return response()->json($groups);
    }

    public function searchAcronymSubgroup(Request $request) {
        $acronym = $request->query('acronym');
    
        if (!$acronym) {
            return response()->json(['exists' => false]);
        }

        $group = Group::where('name', $acronym)->first(['id', 'name']);
        
        if ($group) {
            return response()->json([
                'exists' => true,
                'id' => $group->id,
                'name' => $group->name, 
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }
    

    public function store(Request $request) {
        $messages = [
            'name.unique' => 'Группа с таким названием уже существует. Пожалуйста, выберите другое имя.',
            'admission_year.digits' => 'Год поступления должен состоять из четырех цифр.',
        ];
    
        $validated = $request->validate([
            'name' => 'required|unique:groups,name',
            'admission_year' => 'required|digits:4',
            'education_program_id' => 'required|exists:education_programs,id',
        ], $messages);
    
        $educationProgram = EducationProgram::findOrFail($validated['education_program_id']);
    
        $graduation_year = $validated['admission_year'] + $educationProgram->duration;
    
        $group = Group::create([
            'name' => $validated['name'],
            'admission_year' => $validated['admission_year'],
            'graduation_year' => $graduation_year,
            'education_program_id' => $validated['education_program_id'],
        ]);
    
        return redirect()->back()->with('success', 'Группа успешно создана');
    }

    public function storeSubgroup(Request $request) {
        $messages = [
            'name.unique' => 'Группа с таким названием уже существует. Пожалуйста, выберите другое имя.',
        ];
    
        $validated = $request->validate([
            'name' => 'required|unique:groups,name',
            'group' => 'required', 
            'subgroup' => 'nullable', 
        ], $messages);
    
        $parentGroup = Group::findOrFail($validated['group']);
    
        Group::create([
            'name' => $validated['name'],
            'admission_year' => $parentGroup->admission_year, 
            'graduation_year' => $parentGroup->graduation_year, 
            'education_program_id' => $parentGroup->education_program_id, 
            'subgroup' => $parentGroup->id,
        ]);
    
        return redirect()->back()->with('success', 'Подгруппа успешно создана');
    }
    
    public function search(Request $request) {
        $query = $request->get('search');
    
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
        
        return view('admin.groups', compact('groups'));
    }
    
    public function show(Group $group) {
        return view('admin.show.group', compact('group'));
    }

    public function edit(Group $group) {
        $editing = true;
        $educationPrograms = EducationProgram::all();
        return view('admin.show.group', compact('editing', 'group', 'educationPrograms'));
    }

    public function update(Group $group, Request $request) {
        try {
            $validated = $request->validate([
                'name' => 'required|max:40',
                'admission_year' => 'required|min:4|max:5',
                'graduation_year' => 'required|min:4|max:5',
                'education_program_id' => 'required',
            ]);
            
            $group->update($validated);
    
            return redirect()->route('admin.showGroup', $group->id)
                             ->with('success', 'Образовательная программа успешно обновлена');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Произошла ошибка. Пожалуйста, попробуйте снова.'])->withInput();
        }
    }

    public function detach($groupId, $studentId) {
        $group = Group::findOrFail($groupId);
        $group->students()->detach($studentId);

        return redirect()->back()->with('success', 'Студент успешно исключен из группы');
    }

    public function destroy(Group $group) {
        $group->delete();
        $groups = Group::all();
        return view('admin.groups', compact('groups'))->with('success', 'Группа успешно удалена');
    }

    public function addStudent(Group $group) {
        $students = Student::all();
        return view('admin.add.add-student-to-group', compact('group', 'students'));
    }

    
    public function attachStudent(Group $group, Student $student) {
        $group->students()->attach($student->id);
        return redirect()->back()->with('success', 'Студент добавлен в группу!');
    }
    
    
}
