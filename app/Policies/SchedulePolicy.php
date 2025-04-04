<?php
namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class SchedulePolicy
{
    public function view($user, Schedule $schedule)
    {
        if (Auth::guard('admin')->check()) {
            return true;
        }

        if (Auth::guard('teacher')->check()) {
            $teacher = Teacher::find(Auth::id());
            return $schedule->teacher_id === $teacher->id;
        }

        if (Auth::guard('student')->check()) {
            $student = Student::find(Auth::id());
            return $schedule->group_id === $student->group_id;
        }

        return false;
    }

    public function create($user)
    {
        return Auth::guard('admin')->check() || Auth::guard('teacher')->check();
    }

    public function update($user, Schedule $schedule)
    {
        if (Auth::guard('admin')->check()) {
            return true;
        }

        if (Auth::guard('teacher')->check()) {
            $teacher = Teacher::find(Auth::id());
            return $schedule->teacher_id === $teacher->id;
        }

        return false;
    }

    public function delete($user, Schedule $schedule)
    {
        return $this->update($user, $schedule);
    }
}