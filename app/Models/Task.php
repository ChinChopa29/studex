<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'course_id', 'name', 'description', 'from', 'deadline',
    ];

    public function teacherFiles() {
        return $this->hasMany(TeacherTaskFile::class);
    }

    public function studentrFiles() {
        return $this->hasMany(StudentTaskFile::class);
    }

    public function grades() {
        return $this->hasMany(TaskGrade::class);
    }
}

