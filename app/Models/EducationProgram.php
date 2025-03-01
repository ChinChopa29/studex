<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationProgram extends Model
{
    protected $fillable = [
        'title',
        'acronym',
        'description',
        'duration',
        'degree',
        'price',
        'mode',
    ];
    public function groups()
    {
        return $this->hasMany(Group::class, 'education_program_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'education_program_id');
    }

    public function courses() {
        return $this->belongsToMany(Course::class, 'course_education_program');
    }

}
