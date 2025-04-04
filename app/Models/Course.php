<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{

    protected $fillable = [
        'name', 'code', 'description', 'credits', 'semester', 'type', 'degree',
    ];

    public function teachers() {
        return $this->belongsToMany(Teacher::class, 'teacher_course');
    }

    public function educationPrograms() {
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
}
