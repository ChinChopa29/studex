<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Group;
use App\Models\EducationProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentService 
{
   public function import(Request $request): array
   {
      $educationPrograms = EducationProgram::all();

      $file = $request->file('students');
      $handle = fopen($file->getPathname(), 'r');

      if (!$handle) {
         return $this->error('Ошибка открытия файла.', $educationPrograms);
      }

      $delimiter = $this->detectDelimiter(fgets($handle));
      rewind($handle);
      fgetcsv($handle, 1000, $delimiter); // skip header

      $educationProgramId = (int) $request->input('education_program_id');
      $educationProgram = EducationProgram::find($educationProgramId);
      if (!$educationProgram) {
         return $this->error('Выбранная образовательная программа не найдена.', $educationPrograms);
      }

      $studentIds = [];
      $admissionYear = null;
      $graduationYear = null;

      while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
         if (count($data) < 9) {
               return $this->error('Недостаточно колонок в CSV файле.', $educationPrograms);
         }

         [$surname, $name, $lastname, $iin, $phone, $genderRaw, $birthdayRaw, $admissionYearRaw, $graduationYearRaw] = $data;

         $gender = $this->parseGender($genderRaw);
         if (!$gender) {
               return $this->error('Неверное значение поля "Пол".', $educationPrograms);
         }

         $birthday = \DateTime::createFromFormat('d.m.Y', $birthdayRaw);
         if (!$birthday) {
               return $this->error('Неверное значение поля "Дата рождения".', $educationPrograms);
         }

         if (!is_numeric($admissionYearRaw) || !is_numeric($graduationYearRaw)) {
               return $this->error('Неверное значение года поступления или окончания.', $educationPrograms);
         }

         $student = Student::create([
               'surname' => $surname,
               'name' => $name,
               'lastname' => $lastname,
               'iin' => $iin,
               'phone' => $phone,
               'gender' => $gender,
               'birthday' => $birthday->format('Y-m-d'),
               'admission_year' => (int) $admissionYearRaw,
               'graduation_year' => (int) $graduationYearRaw,
               'education_program_id' => $educationProgramId,
         ]);

         $studentIds[] = $student->id;
         $admissionYear = (int) $admissionYearRaw;
         $graduationYear = (int) $graduationYearRaw;
      }

      fclose($handle);

      $group = $this->createGroup($educationProgram, $admissionYear, $graduationYear);
      $group->students()->attach($studentIds);

      return [
         'status' => 'success',
         'message' => 'Студенты успешно загружены!',
         'educationPrograms' => $educationPrograms
      ];
   }

   protected function error(string $message, $educationPrograms): array
   {
      return [
         'status' => 'error',
         'message' => $message,
         'educationPrograms' => $educationPrograms
      ];
   }

   protected function detectDelimiter(string $line): string
   {
      $delimiters = [",", ";", "\t"];
      $counts = [];

      foreach ($delimiters as $delimiter) {
         $counts[$delimiter] = count(str_getcsv($line, $delimiter));
      }

      arsort($counts);
      return key($counts);
   }

   protected function parseGender(string $raw): ?string
   {
        return match (trim($raw)) {
            'М' => 'Мужской',
            'Ж' => 'Женский',
            default => null,
        };
   }

   protected function createGroup(EducationProgram $program, int $admission, int $graduation): Group
   {
        $baseName = $program->acronym . $admission;
        $existingNames = Group::where('name', 'LIKE', "$baseName%")->pluck('name')->toArray();

        $usedLetters = array_map(fn($name) => substr($name, strlen($baseName) + 1), $existingNames);
        $letter = collect(range('a', 'z'))->first(fn($char) => !in_array($char, $usedLetters)) ?? 'z';

        return Group::create([
            'name' => "$baseName-$letter",
            'admission_year' => $admission,
            'graduation_year' => $graduation,
            'education_program_id' => $program->id,
        ]);
   }

   public function searchStudents(?string $query, int $perPage = 10)
   {
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

      return $students->paginate($perPage)->appends(request()->query());
   }

   public function updateStudent(Student $student, array $data): Student
   {
      if (!empty($data['plain_password'])) {
         $data['password'] = Hash::make($data['plain_password']);
         $student->plain_password = $data['plain_password']; 
      }
      unset($data['plain_password']);

      $student->update($data);

      return $student;
   }

   public function assignEmailsToStudentsWithoutEmail(): int
   {
      $students = Student::whereNull('email')->get();

      if ($students->isEmpty()) {
         return 0;
      }

      foreach ($students as $student) {
         $group = $student->groups->first();
         $groupId = $group ? $group->id : '00';

         $studentId = $student->id > 100 ? substr($student->id, -2) : $student->id;

         $email = "{$student->admission_year}{$groupId}0{$studentId}@studex.com";

         $plainPassword = Str::random(8);
         $hashedPassword = bcrypt($plainPassword);

         $student->email = $email;
         $student->plain_password = $plainPassword; // если нужно хранить
         $student->password = $hashedPassword;
         $student->save();
      }

      return $students->count();
   }
}