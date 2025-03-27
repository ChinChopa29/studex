<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name', 'surname', 'lastname', 'iin', 'phone', 'gender', 'birthday',
        'admission_year', 'graduation_year', 'education_program_id', 
        'email', 'password',
    ];

    protected $hidden = ['plain_password'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_student');
    }
    
    public function courses() {
        return $this->belongsToMany(Course::class, 'student_course')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function educationProgram()
    {
        return $this->belongsTo(EducationProgram::class, 'education_program_id');
    }
    
    public function grades() 
    {
        return $this->hasMany(TaskGrade::class);
    }
    
}
