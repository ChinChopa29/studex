<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Group;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AccountingService
{
    public function getGroupWithStudents(Course $course)
    {
        return Group::whereHas('students', function ($query) use ($course) {
            $query->whereHas('courses', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            });
        })->with(['students' => function ($query) use ($course) {
            $query->with(['courses' => function ($q) use ($course) {
                $q->where('course_id', $course->id)->select('student_id', 'status');
            }]);
        }])->get();
    }

    protected function exportGroupReport(Course $course, int $groupId, string $filenamePrefix, callable $getHeaders, callable $fillStudentData): string 
    {
        $group = Group::findOrFail($groupId);
        $students = $group->students()->get();
        $milestones = $course->milestones()->orderBy('deadline')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = array_merge(['Группа', 'ФИО студента'], $getHeaders($milestones, $group, $course));
        $sheet->fromArray([$headers], null, 'A1');

        $row = 2;
        foreach ($students as $student) {
            $studentData = $fillStudentData($student, $milestones, $group, $course);
            $sheet->fromArray($studentData, null, "A{$row}");
            $row++;
        }

        $lastColumn = $sheet->getHighestColumn();
        foreach (range('A', $lastColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        if (ob_get_contents()) ob_clean();
        if (ob_get_length()) ob_end_clean();

        $filename = "{$filenamePrefix}_{$course->name}_{$group->name}.xlsx";
        $filePath = storage_path('app/' . $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        chmod($filePath, 0777);

        return $filePath;
    }

    protected function resolveGroupForStudent(Student $student, ?int $groupId): Group
    {
        if ($groupId) {
            $group = Group::findOrFail($groupId);
            if (!$student->groups->contains($group->id)) {
                throw new \Exception('Студент не принадлежит к этой группе.');
            }
        } else {
            $group = $student->groups->first();
            if (!$group) {
                throw new \Exception('У студента нет группы.');
            }
        }
        return $group;
    }

    public function showStudentData(Course $course, Student $student, ?int $groupId, int $perPage, callable $dataLoader, callable $statisticsCalculator): array 
    {
        $group = $this->resolveGroupForStudent($student, $groupId);

        $data = $dataLoader($course, $student, $group, $perPage);
        $stats = $statisticsCalculator($course, $student, $group, $data);

        return array_merge(
            ['course' => $course, 'student' => $student, 'group' => $group],
            $data,
            $stats
        );
    }

    protected function createSpreadsheet(array $headers, array $dataRows, string $rangeEnd = 'Z'): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([$headers], null, 'A1');
        $sheet->fromArray($dataRows, null, 'A2');

        foreach (range('A', $rangeEnd) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle("A1:{$rangeEnd}1")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID],
        ]);

        return $spreadsheet;
    }

    protected function saveSpreadsheet(Spreadsheet $spreadsheet, string $filename): string
    {
        if (ob_get_contents()) ob_clean();
        if (ob_get_length()) ob_end_clean();

        $path = storage_path("app/{$filename}");
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    public function exportGroupAttendance(Course $course, int $groupId): string
    {
        return $this->exportGroupReport(
            $course,
            $groupId,
            'Отчет_посещаемости',
            function($milestones, $group, $course) {
                return $course->schedules()
                    ->where('group_id', $group->id)
                    ->select('milestone_id')
                    ->distinct()
                    ->get()
                    ->map(fn($schedule) => "РК{$schedule->milestone_id}")
                    ->toArray() + ['Общий процент посещаемости'];
            },
            function($student, $milestones, $group, $course) {
                $studentData = [
                    $group->name,
                    "{$student->surname} {$student->name} {$student->lastname}"
                ];

                $totalLessons = $course->schedules()->where('group_id', $group->id)->count();
                $totalAttended = 0;

                foreach ($course->schedules->where('group_id', $group->id)->groupBy('milestone_id') as $milestoneId => $schedules) {
                    $milestoneLessons = $schedules->count();

                    $attendedMilestone = $student->attendances()
                        ->whereHas('lesson', fn($q) => 
                            $q->where('milestone_id', $milestoneId)
                            ->where('group_id', $group->id)
                        )
                        ->whereIn('status', ['present', 'late'])
                        ->count();

                    $studentData[] = "{$attendedMilestone} / {$milestoneLessons}";
                    $totalAttended += $attendedMilestone;
                }

                $percentage = $totalLessons > 0 ? round(($totalAttended / $totalLessons) * 100) : 0;
                $studentData[] = "{$percentage}%";

                return $studentData;
            }
        );
    }

    public function exportGroupAssignment(Course $course, int $groupId): string
    {
        return $this->exportGroupReport(
            $course,
            $groupId,
            'Отчет_по_заданиям',
            fn($milestones, $group, $course) => 
                array_merge(
                    $milestones->map(fn($m) => $m->name . ' (' . $m->deadline->format('d.m.Y') . ')')->toArray(),
                    ['Общий прогресс']
                ),
            function($student, $milestones, $group, $course) {
                $studentData = [
                    $group->name,
                    "{$student->surname} {$student->name} {$student->lastname}"
                ];

                $totalTasks = 0;
                $submittedTasks = 0;
                $reviewedTasks = 0;

                foreach ($milestones as $milestone) {
                    $allTasks = $milestone->tasks->concat($milestone->testTasks);
                    $totalMilestoneTasks = $allTasks->count();
                    $submittedMilestoneTasks = 0;
                    $reviewedMilestoneTasks = 0;

                    foreach ($allTasks as $task) {
                        if ($milestone->testTasks->contains($task)) {
                            $testResult = \App\Models\TestResult::where('student_id', $student->id)
                                ->where('test_task_id', $task->id)
                                ->first();

                            if ($testResult && $testResult->score > 0) { 
                                $reviewedMilestoneTasks++;
                                $submittedMilestoneTasks++;
                                continue;
                            }
                        } else {
                            $grade = $student->grades()->where('task_id', $task->id)->first();
                            if ($grade) {
                                $reviewedMilestoneTasks++;
                                $submittedMilestoneTasks++;
                                continue;
                            }

                            $file = \App\Models\StudentTaskFile::where('student_id', $student->id)
                                ->where('task_id', $task->id)
                                ->first();

                            $comment = \App\Models\TaskComment::where('student_id', $student->id)
                                ->where('task_id', $task->id)
                                ->first();

                            if ($file || $comment) {
                                $submittedMilestoneTasks++;
                            }
                        }
                    }

                    $milestonePercentage = $totalMilestoneTasks > 0 
                        ? round(($submittedMilestoneTasks / $totalMilestoneTasks) * 100)
                        : 0;

                    $studentData[] = "{$reviewedMilestoneTasks}/{$submittedMilestoneTasks}/{$totalMilestoneTasks} ({$milestonePercentage}%)";

                    $totalTasks += $totalMilestoneTasks;
                    $submittedTasks += $submittedMilestoneTasks;
                    $reviewedTasks += $reviewedMilestoneTasks;
                }

                $overallPercentage = $totalTasks > 0 
                    ? round(($submittedTasks / $totalTasks) * 100)
                    : 0;

                $studentData[] = "{$reviewedTasks}/{$submittedTasks}/{$totalTasks} ({$overallPercentage}%)";

                return $studentData;
            }
        );
    }

    public function exportGroupPerfomance(Course $course, int $groupId): string
    {
        return $this->exportGroupReport(
            $course,
            $groupId,
            'Отчет_по_успеваемости',
            fn($milestones, $group, $course) =>
                array_merge(
                    $milestones->map(fn($m) => $m->name . ' (' . $m->deadline->format('d.m.Y') . ')')->toArray(),
                    ['Оценка за семестр']
                ),
            function($student, $milestones, $group, $course) {
                $studentData = [
                    $group->name,
                    "{$student->surname} {$student->name} {$student->lastname}"
                ];

                $totalTasks = 0;
                $totalGrades = 0;

                foreach ($milestones as $milestone) {
                    $tasks = $milestone->tasks->concat($milestone->testTasks);
                    $milestoneGrades = 0;
                    $milestoneTotal = 0;

                    foreach ($tasks as $task) {
                        $grade = $student->grades()->where('task_id', $task->id)->first();

                        if ($grade) {
                            $milestoneGrades += $grade->grade;
                            $milestoneTotal++;
                        }
                    }

                    $milestoneAverage = $milestoneTotal > 0 
                        ? round($milestoneGrades / $milestoneTotal, 2)
                        : 0;  

                    $studentData[] = $milestoneAverage;

                    if ($milestoneAverage > 0) {
                        $totalGrades += $milestoneAverage;
                    }
                    $totalTasks++;
                }

                $semesterGrade = $totalTasks > 0 ? round($totalGrades / $totalTasks, 2) : 0;

                $studentData[] = $semesterGrade;

                return $studentData;
            }
        );
    }

    public function showStudentAttendance(Course $course, Student $student, ?int $groupId = null, int $perPage = 15): array
    {
        return $this->showStudentData($course, $student, $groupId, $perPage,
            function($course, $student, $group, $perPage) {
                $lessonsQuery = $course->schedules()
                    ->where('group_id', $group->id)
                    ->orderBy('date', 'desc');
                return ['lessonsQuery' => $lessonsQuery];
            },
            function($course, $student, $group, $data) {
                $lessonsQuery = $data['lessonsQuery'];
                $totalLessons = $lessonsQuery->count();
                $lessonIds = $lessonsQuery->pluck('id');

                $attendances = Attendance::where('student_id', $student->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->get()
                    ->keyBy('lesson_id');

                $attendedCount = $attendances->where('status', 'present')->count();
                $lateCount = $attendances->where('status', 'late')->count();

                $attendancePercentage = $totalLessons > 0
                    ? round(($attendedCount + $lateCount) / $totalLessons * 100)
                    : 0;

                $lessons = $lessonsQuery->paginate(15);

                return compact('lessons', 'totalLessons', 'attendedCount', 'lateCount','attendancePercentage', 'attendances' 
                );
            }
        );
    }

    public function showStudentAssignment(Course $course, Student $student, ?int $groupId = null, int $perPage = 15): array
    {
        return $this->showStudentData( $course, $student, $groupId, $perPage,
            function($course, $student, $group, $perPage) {
                $milestones = $course->milestones()
                    ->with(['tasks' => function($query) {
                        $query->orderBy('deadline', 'asc');
                    }])
                    ->orderBy('deadline', 'asc')
                    ->get();
                return ['milestones' => $milestones];
            },
            function($course, $student, $group, $data) {
                $milestones = $data['milestones'];

                $totalTasks = 0;
                $submittedTasks = 0;
                $reviewedTasks = 0;

                foreach ($milestones as $milestone) {
                    foreach ($milestone->tasks as $task) {
                        $totalTasks++;

                        $file = \App\Models\StudentTaskFile::where('student_id', $student->id)
                            ->where('task_id', $task->id)
                            ->first();
                        $comment = \App\Models\TaskComment::where('student_id', $student->id)
                            ->where('task_id', $task->id)
                            ->first();

                        if ($file || $comment) {
                            $submittedTasks++;
                        }

                        $grade = $student->grades()->where('task_id', $task->id)->first();
                        if ($grade) {
                            $reviewedTasks++;
                        }
                    }
                }

                $progressPercentage = $totalTasks > 0
                    ? round(($submittedTasks / $totalTasks) * 100)
                    : 0;

                return compact('milestones', 'totalTasks', 'submittedTasks', 'reviewedTasks', 'progressPercentage');
            }
        );
    }

    public function exportStudentAttendance(Course $course, Student $student, ?int $groupId = null): string
    {
        $group = Group::findOrFail($groupId);

        $lessons = $course->schedules()
            ->where('group_id', $group->id)
            ->with(['attendances' => fn($q) => $q->where('student_id', $student->id)])
            ->orderBy('date', 'asc')
            ->get();

        $rows = [];
        foreach ($lessons as $lesson) {
            $attendance = $lesson->attendances->first();
            $status = $attendance ? $attendance->status : 'absent';

            $statusText = [
                'present' => 'Присутствовал',
                'late' => 'Опоздал',
                'absent' => 'Отсутствовал'
            ][$status];

            $rows[] = [
                $lesson->date->format('d.m.Y'),
                $lesson->start_time->format('H:i') . ' - ' . $lesson->end_time->format('H:i'),
                $lesson->title ?? 'Тема не указана',
                $statusText
            ];
        }

        $spreadsheet = $this->createSpreadsheet(
            ['Дата', 'Время', 'Тема занятия', 'Статус посещения'],
            $rows,
            'D'
        );

        $filename = "Посещаемость_{$student->surname}_{$student->name}_{$course->name}.xlsx";
        return $this->saveSpreadsheet($spreadsheet, $filename);
    }

    public function exportStudentAssignment(Course $course, Student $student, ?int $groupId = null): string
    {
        $group = Group::findOrFail($groupId);

        $milestones = $course->milestones()
            ->with(['tasks' => fn($q) => $q->orderBy('deadline', 'asc')])
            ->orderBy('deadline', 'asc')
            ->get();

        $rows = [];

        foreach ($milestones as $milestone) {
            foreach ($milestone->tasks as $task) {
                $grade = $student->grades()->where('task_id', $task->id)->first();
                $file = \App\Models\StudentTaskFile::where('student_id', $student->id)->where('task_id', $task->id)->first();
                $comment = \App\Models\TaskComment::where('student_id', $student->id)->where('task_id', $task->id)->first();

                $status = 'Не сдано';
                if ($grade) {
                    $status = 'Проверено';
                } elseif ($file || $comment) {
                    $status = 'Сдано';
                }

                $rows[] = [
                    $milestone->name,
                    $task->title,
                    $task->description ?? 'Нет описания',
                    $task->deadline->format('d.m.Y'),
                    $status,
                    $grade ? "{$grade->grade}/100" : '-',
                    $grade ? $grade->comment : ($comment->comment ?? '-')
                ];
            }
        }

        $spreadsheet = $this->createSpreadsheet(
            ['Этап', 'Задание', 'Описание', 'Срок сдачи', 'Статус', 'Оценка', 'Комментарий преподавателя'],
            $rows,
            'G'
        );

        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        for ($i = 2; $i <= $highestRow; $i++) {
            $deadline = $sheet->getCell("D{$i}")->getValue();
            $status = $sheet->getCell("E{$i}")->getValue();

            if ($status === 'Не сдано' && \Carbon\Carbon::createFromFormat('d.m.Y', $deadline)->isPast()) {
                $sheet->getStyle("A{$i}:G{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFDDDD']
                    ]
                ]);
            }
        }

        $filename = "Выполнение_заданий_{$student->surname}_{$student->name}_{$course->name}.xlsx";
        return $this->saveSpreadsheet($spreadsheet, $filename);
    }
}
