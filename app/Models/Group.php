<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'admission_year', 'graduation_year', 'education_program_id', 'subgroup',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'group_student');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'group_course');
    }

    public function educationProgram()
    {
        return $this->belongsTo(EducationProgram::class, 'education_program_id');
    }

    public function teachers() {
        return $this->belongsToMany(Teacher::class, 'teacher_group');
    }

}
