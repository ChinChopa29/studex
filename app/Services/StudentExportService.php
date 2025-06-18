<?php

namespace App\Services;

use App\Models\Student;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentExportService
{
   public function getStudentsQuery()
   {
      $query = Student::with('groups')->whereNotNull('email');

      if (request()->has('group') && request()->group != 'all') {
         $query->whereHas('groups', function ($q) {
               $q->where('groups.id', request()->group);
         });
      }

      return $query;
   }

   public function prepareStudentsData($students)
   {
      return $students->sortByDesc(function ($student) {
         return $student->groups->first()->name ?? 'Без группы';
      });
   }

   public function generateSpreadsheet($students)
   {
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

      return $spreadsheet;
   }

   public function saveAndDownloadSpreadsheet($spreadsheet, $groupName)
   {
      ob_clean();
      ob_end_flush();
      
      $filePath = storage_path('students'. $groupName .'.xlsx');
      $writer = new Xlsx($spreadsheet);
      $writer->save($filePath);
      chmod($filePath, 0777);
      
      return response()->download($filePath)->deleteFileAfterSend();
   }
}