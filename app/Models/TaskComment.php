<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $fillable = [
        'task_id', 'student_id', 'comment',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
