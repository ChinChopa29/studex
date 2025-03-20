<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskGrade extends Model
{
    protected $fillable = [
        'task_id', 'student_id', 'grade',
    ];

    public function task() {
        return $this->belongsTo(Task::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }
}

