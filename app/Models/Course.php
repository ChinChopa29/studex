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
}
