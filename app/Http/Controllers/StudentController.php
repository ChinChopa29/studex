<?php

namespace App\Http\Controllers;

use App\Models\EducationProgram;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends Controller
{
    public function index() {
        $students = Student::paginate(10);
        return view('admin.students', compact('students'));
    }

    public function create() {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-student', compact('educationPrograms'));
    }

    public function store(Request $request)
    {
        $educationPrograms = EducationProgram::all();
    
        if (!$request->hasFile('students')) {
            return redirect()->back()->with('error', 'Выберите CSV файл')->with(compact('educationPrograms'));
        }
    
        $request->validate([
            'students' => 'required|file|mimes:csv,txt'
        ]);
    
        $file = $request->file('students');

        $handle = fopen($file->getPathname(), "r");
        if (!$handle) {
            return redirect()->back()->with('error', 'Ошибка открытия файла.');
        }
    
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = $this->detectDelimiter($firstLine);
    
        fgetcsv($handle, 1000, $delimiter);
    
        $educationProgramId = (int) $request->input('education_program_id');
        $educationProgram = EducationProgram::find($educationProgramId);
    
        if (!$educationProgramId) {
            return redirect()->back()->with('error', 'Выберите образовательную программу')->with(compact('educationPrograms'));
        }
    
        $studentIds = [];
        $admissionYear = null;
        $graduationYear = null;
    
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            if (count($data) < 9) {
                return redirect()->back()->with('error', 'Недостаточно колонок в CSV файле')->with(compact('educationPrograms'));
                continue;
            }
    
            [$surname, $name, $lastname, $iin, $phone, $genderRaw, $birthdayRaw, $admissionYearRaw, $graduationYearRaw] = $data;
    
            $gender = $genderRaw === 'М' ? 'Мужской' : ($genderRaw === 'Ж' ? 'Женский' : null);
            if (!$gender) {
                return redirect()->back()->with('error', 'Неверное значение поля "Пол"')->with(compact('educationPrograms'));
            }
    
            $birthday = \DateTime::createFromFormat('d.m.Y', $birthdayRaw);
            if (!$birthday) {
                return redirect()->back()->with('error', 'Неверное значение или формат поля "Дата рождения"')->with(compact('educationPrograms'));
            }
            $birthday = $birthday->format('Y-m-d');
    
            if (!is_numeric($admissionYearRaw) || !is_numeric($graduationYearRaw)) {
                return redirect()->back()->with('error', 'Неверное значение года поступления или года окончания')->with(compact('educationPrograms'));
            }
    
            $student = Student::create([
                'surname' => $surname,
                'name' => $name,
                'lastname' => $lastname,
                'iin' => $iin,
                'phone' => $phone,
                'gender' => $gender,
                'birthday' => $birthday,
                'admission_year' => (int) $admissionYearRaw,
                'graduation_year' => (int) $graduationYearRaw,
                'education_program_id' => $educationProgramId,
            ]);
    
            $studentIds[] = $student->id;
            $admissionYear = (int) $admissionYearRaw;
            $graduationYear = (int) $graduationYearRaw;
        }
    
        fclose($handle);
    
        $baseName = $educationProgram->acronym . $admissionYear;
        $existingGroups = Group::where('name', 'LIKE', "$baseName%")->pluck('name')->toArray();
    
        $letter = 'a';
        if (!empty($existingGroups)) {
            $usedLetters = array_map(function ($name) use ($baseName) {
                return substr($name, strlen($baseName) + 1);
            }, $existingGroups);
    
            foreach (range('a', 'z') as $char) {
                if (!in_array($char, $usedLetters)) {
                    $letter = $char;
                    break;
                }
            }
        }
    
        $group = Group::create([
            'name' => $baseName . '-' . $letter,
            'admission_year' => $admissionYear,
            'graduation_year' => $graduationYear,
            'education_program_id' => $educationProgramId,
        ]);
    
        if (!empty($studentIds)) {
            $group->students()->attach($studentIds);
        }
    
        return redirect()->back()->with('success', 'Студенты успешно загружены!')->with(compact('educationPrograms'));
    }
    
    private function detectDelimiter($line)
    {
        $delimiters = [",", ";", "\t"];
        $counts = [];
        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($line, $delimiter);
        }
        return array_search(max($counts), $counts);
    }
    
    public function search(Request $request) {
        $query = $request->get('search');
        $students = Student::with('groups');
    
        if ($query) {
            $students->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                  ->orWhere('surname', 'LIKE', "%$query%")
                  ->orWhere('lastname', 'LIKE', "%$query%")
                  ->orWhere('iin', 'LIKE', "%$query%")
                  ->orWhereHas('groups', function ($q) use ($query) {
                      $q->where('name', 'LIKE', "%$query%");
                  });
            });
        }
        
        $students = $students->paginate(10)->appends(request()->query());
        
        return view('admin.students', compact('students'));
    }

    public function show(Student $student)
    {   
        session(['return_to' => url()->previous()]); 
        return view('admin.show.student', compact('student'));
    }

    public function edit(Student $student) {
        $editing = true;
        $educationPrograms = EducationProgram::all();
        return view('admin.show.student', compact('student', 'editing', 'educationPrograms'));
    }

    public function update(Student $student, Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'lastname' => 'required',
            'iin' => 'required|size:12',
            'phone' => 'required',
            'gender' => 'required|in:Мужской,Женский',
            'birthday' => 'required|date',
            'admission_year' => 'required|digits:4',
            'graduation_year' => 'required|digits:4',
            'education_program_id' => 'required|exists:education_programs,id',
        ], [
            'name.required' => 'Поле "Имя" обязательно.',
            'surname.required' => 'Поле "Фамилия" обязательно.',
            'lastname.required' => 'Поле "Отчество" обязательно.',
            'iin.required' => 'Поле "ИИН" обязательно.',
            'iin.size' => 'ИИН должен содержать 12 цифр.',
            'phone.required' => 'Поле "Телефон" обязательно.',
            'gender.required' => 'Выберите пол.',
            'birthday.required' => 'Введите дату рождения.',
            'birthday.date' => 'Дата рождения должна быть корректной.',
            'admission_year.required' => 'Введите год поступления.',
            'admission_year.digits' => 'Год поступления должен содержать 4 цифры.',
            'graduation_year.required' => 'Введите год окончания.',
            'graduation_year.digits' => 'Год окончания должен содержать 4 цифры.',
            'education_program_id.required' => 'Выберите образовательную программу.',
            'education_program_id.exists' => 'Выбранная образовательная программа не существует.',
        ]);

        $student->update($validated);

        return redirect()->back()->with('success', 'Студент успешно обновлен!');
    }   

    public function destroy(Student $student) {
        $student->delete();
        return redirect()->route('admin.showUsers')->with('success', 'Студент успешно удален');
    }

    public function createOne() {
        $educationPrograms = EducationProgram::all();
        return view('admin.add.add-student-one', compact('educationPrograms'));
    }

    public function storeOne(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'lastname' => 'required',
            'iin' => 'required|size:12',
            'phone' => 'required',
            'gender' => 'required|in:Мужской,Женский',
            'birthday' => 'required|date',
            'admission_year' => 'required|digits:4',
            'graduation_year' => 'required|digits:4',
            'education_program_id' => 'required|exists:education_programs,id',
        ], [
            'name.required' => 'Поле "Имя" обязательно.',
            'surname.required' => 'Поле "Фамилия" обязательно.',
            'lastname.required' => 'Поле "Отчество" обязательно.',
            'iin.required' => 'Поле "ИИН" обязательно.',
            'iin.size' => 'ИИН должен содержать 12 цифр.',
            'phone.required' => 'Поле "Телефон" обязательно.',
            'gender.required' => 'Выберите пол.',
            'birthday.required' => 'Введите дату рождения.',
            'birthday.date' => 'Дата рождения должна быть корректной.',
            'admission_year.required' => 'Введите год поступления.',
            'admission_year.digits' => 'Год поступления должен содержать 4 цифры.',
            'graduation_year.required' => 'Введите год окончания.',
            'graduation_year.digits' => 'Год окончания должен содержать 4 цифры.',
            'education_program_id.required' => 'Выберите образовательную программу.',
            'education_program_id.exists' => 'Выбранная образовательная программа не существует.',
        ]);
    
        Student::create($validated);
    
        return redirect()->route('admin.createUser')->with('success', 'Студент успешно добавлен!');
    }

    public function assignEmailsForm() {
        $students = Student::all();
        $groups = Group::all();
        return view('admin.add.assign-email', compact('students', 'groups'));
    }
    
    public function downloadEmailsForm() {
        $groups = Group::all();
        return view('admin.add.download-students', compact('groups'));
    }
    
    public function assignEmails() {
        $students = Student::whereNull('email')->get();
    
        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Нет студентов для выдачи почт и паролей');
        }
    
        foreach ($students as $student) {
            $group = $student->groups->first(); 
            $groupId = $group ? $group->id : '00'; 
    
            $studentId = $student->id > 100 ? substr($student->id, -2) : $student->id;
    
            $email = "{$student->admission_year}{$groupId}0{$studentId}@studex.com";
            
            $plainPassword = Str::random(8); 
            $hashedPassword = bcrypt($plainPassword); 
    
            $student->email = $email;
            $student->plain_password = $plainPassword; 
            $student->password = $hashedPassword;
            $student->save();
        }
    
        return redirect()->back()->with('success', 'Почты и пароли успешно выданы');
    }

    public function downloadEmails()
    {
        $query = Student::with('groups')->whereNotNull('email');

        if (request()->has('group') && request()->group != 'all') {
            $query->whereHas('groups', function ($q) {
                $q->where('groups.id', request()->group);
            });
        }

        $students = $query->get();

        $students = $students->sortBy(function ($student) {
            return $student->groups->first()->name ?? 'Без группы';
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ФИО', 'Группа', 'Email', 'Пароль'];
        $sheet->fromArray([$headers], null, 'A1');

        $row = 2;
        foreach ($students as $student) {
            $group = $student->groups->first();
            $groupName = $group ? $group->name : 'Без группы';

            $sheet->fromArray([
                "{$student->surname} {$student->name} {$student->lastname}",
                $groupName,
                $student->email,
                $student->plain_password,
            ], null, "A{$row}");

            $row++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filePath = storage_path('students'. $groupName .'.xlsx');
        $writer->save($filePath);

        return Response::download($filePath)->deleteFileAfterSend();
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