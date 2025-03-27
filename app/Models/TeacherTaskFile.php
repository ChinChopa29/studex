<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherTaskFile extends Model
{
    protected $fillable = [
        'task_id', 'teacher_id', 'file_path', 'original_name',
    ];

    public function task() {
        return $this->belongsTo(Task::class);
    }

    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }
}

