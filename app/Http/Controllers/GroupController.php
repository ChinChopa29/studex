<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\StoreSubGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\EducationProgram;
use App\Models\Group;
use App\Models\Student;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function index() 
    {
        $groups = Group::paginate(10);
        return view('admin.groups', compact('groups'));
    }

    public function show(Group $group) 
    {
        $teachers = $this->groupService->getAllTeachers();
        return view('admin.show.group', compact('group', 'teachers'));
    }
    
    public function create() 
    {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-group', compact('educationPrograms'));
    }

    public function store(StoreGroupRequest $request) 
    {
        $validated = $request->validated();
        $this->groupService->createGroup($validated);
        return redirect()->back()
            ->with('success', 'Группа успешно создана');
    }

    public function createSubgroup() 
    {
        $groups = Group::all();
        return view('admin.add.add-subgroup', compact('groups'));
    }

    public function storeSubgroup(StoreSubGroupRequest $request) 
    {
        $validated = $request->validated();
        $this->groupService->createSubGroup($validated);
        return redirect()->back()
            ->with('success', 'Подгруппа успешно создана');
    }
    
    public function search(Request $request) 
    {
        $query = $request->get('search');
        $groups = $this->groupService->searchGroups($query);
        return view('admin.groups', compact('groups'));
    }
    
    public function edit(Group $group) 
    {
        $editing = true;
        $teachers = $this->groupService->getAllTeachers();
        $educationPrograms = EducationProgram::all();
        return view('admin.show.group', compact('editing', 'group', 'educationPrograms', 'teachers'));
    }

    public function update(Group $group, UpdateGroupRequest $request) 
    {
        $validated = $request->validated();
        
        if (!empty($validated['teacher'])) {
            $this->groupService->attachTeacher($group, $validated['teacher']);
        }

        $this->groupService->updateGroup($group, $validated);

        return redirect()->route('admin.showGroup', $group->id)
            ->with('success', 'Образовательная программа успешно обновлена');
    }

    public function detachTeacher($groupId, $teacherId) 
    {
        $group = Group::findOrFail($groupId);
        $this->groupService->detachTeacher($group, $teacherId);
        return redirect()->back()
            ->with('success', 'Преподаватель успешно исключен из группы');
    }

    public function addStudent(Group $group) 
    {
        $students = Student::all();
        return view('admin.add.add-student-to-group', compact('group', 'students'));
    }

    public function attachStudent(Group $group, Student $student) 
    {
        $this->groupService->attachStudent($group, $student);
        return redirect()->back()->with('success', 'Студент добавлен в группу!');
    }

    public function detachStudent($groupId, $studentId) 
    {
        $group = Group::findOrFail($groupId);
        $this->groupService->detachStudent($group, $studentId);
        return redirect()->back()
            ->with('success', 'Студент успешно исключен из группы');
    }

    public function destroy(Group $group) 
    {
        $this->groupService->deleteGroup($group);
        return redirect()->route('admin.groups')->with('success', 'Группа успешно удалена');
    }

    public function searchAcronym(Request $request) 
    {
        $acronym = $request->query('acronym');
            
        if (!$acronym) {
            return response()->json([]);
        }
    
        $groups = Group::where('name', 'LIKE', "$acronym%")->get(['id', 'name']);
        
        return response()->json($groups);
    }

    public function searchAcronymSubgroup(Request $request) 
    {
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

    public function showGroups()
    {
        $groups = Group::all();
        return view('groups', compact('groups'));
    }
}
