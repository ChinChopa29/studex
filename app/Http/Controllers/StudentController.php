<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportStudentRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\EducationProgram;
use App\Models\Group;
use App\Models\Student;
use App\Services\StudentExportService;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index() 
    {
        $students = Student::paginate(10);
        return view('admin.students', compact('students'));
    }

    public function create() 
    {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-student', compact('educationPrograms'));
    }

    public function store(ImportStudentRequest $request)
    {
        $result = $this->studentService->import($request);

        return redirect()->back()
            ->with($result['status'], $result['message'])
            ->with('educationPrograms', $result['educationPrograms']);
    }
    
    public function search(Request $request)
    {
        $query = $request->get('search');
        $students = $this->studentService->searchStudents($query);

        return view('admin.students', compact('students'));
    }

    public function show(Student $student)
    {   
        session(['return_to' => url()->previous()]); 
        return view('admin.show.student', compact('student'));
    }

    public function edit(Student $student) 
    {
        $editing = true;
        $educationPrograms = EducationProgram::all();
        return view('admin.show.student', compact('student', 'editing', 'educationPrograms'));
    }

    public function update(Student $student, UpdateStudentRequest $request)
    {
        $this->studentService->updateStudent($student, $request->validated());

        return redirect()->route('admin.showUser', $student->id)
            ->with('success', 'Студент успешно обновлен!');
    }
    
    public function destroy(Student $student) 
    {
        $student->delete();
        return redirect()->route('admin.showUsers')->with('success', 'Студент успешно удален');
    }

    public function createOne() 
    {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-student-one', compact('educationPrograms'));
    }

    public function storeOne(StoreStudentRequest $request) 
    {
        $validated = $request->validated();
        Student::create($validated);
        return redirect()->route('admin.createUser')->with('success', 'Студент успешно добавлен!');
    }

    public function assignEmailsForm() 
    {
        $students = Student::all();
        $groups = Group::all();
        return view('admin.add.assign-email', compact('students', 'groups'));
    }
    
    public function downloadEmailsForm() 
    {
        $groups = Group::all();
        return view('admin.add.download-students', compact('groups'));
    }
    
    public function assignEmails()
    {
        $count = $this->studentService->assignEmailsToStudentsWithoutEmail();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Нет студентов для выдачи почт и паролей');
        }

        return redirect()->back()->with('success', "Почты и пароли успешно выданы {$count} студентам");
    }

    public function downloadEmails()
    {
        $exportService = new StudentExportService();
        
        $query = $exportService->getStudentsQuery();
        $students = $query->get();
        $students = $exportService->prepareStudentsData($students);
        
        $spreadsheet = $exportService->generateSpreadsheet($students);
        
        $groupName = $students->first()?->groups->first()?->name ?? 'Без группы';
        
        return $exportService->saveAndDownloadSpreadsheet($spreadsheet, $groupName);
    }

    public function resetEmailsAndPasswords()
    {
        Student::query()->update([
            'email' => null,
            'password' => null,
            'plain_password' => null
        ]);

        return redirect()->back()->with('success', 'Все email и пароли успешно очищены!');
    }
}