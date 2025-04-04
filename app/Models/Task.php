<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = 
    [
        'course_id', 'name', 'description', 'from', 'deadline', 'milestone_id',
    ];

    public function teacherFiles(): HasMany
    {
        return $this->hasMany(TeacherTaskFile::class);
    }

    public function studentFiles(): HasMany
    {
        return $this->hasMany(StudentTaskFile::class);
    }

    public function grades() 
    {
        return $this->hasMany(TaskGrade::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }
    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}

