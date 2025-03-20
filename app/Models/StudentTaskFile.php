<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTaskFile extends Model
{
    protected $fillable = [
        'task_id', 'student_id', 'file_path',
    ];

    public function task() {
        return $this->belongsTo(Task::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }
}
