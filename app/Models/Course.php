<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Course extends Model
{

    protected $fillable = [
        'name', 'code', 'description', 'credits', 'semester', 'type', 'degree', 'hours',
    ];

    public function teachers() 
    {
        return $this->belongsToMany(Teacher::class, 'teacher_course');
    }

    public function educationPrograms() 
    {
        return $this->belongsToMany(EducationProgram::class, 'course_education_program');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_course')
                ->withPivot('status');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_course');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function schedules() 
    {
        return $this->hasMany(Schedule::class);
    }

    public function testTasks()
    {
        return $this->hasMany(TestTask::class);
    }

    public function style()
    {
        return $this->hasOne(CourseStyle::class)->where('student_id', Auth::id());
    }
}
